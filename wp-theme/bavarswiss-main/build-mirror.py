#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Локализатор статики bavarswiss.ru (NetHouse) -> самостоятельная статика.
Ассеты сохраняются под безопасными именами (hash+ext), ссылки перепривязываются."""
import os, re, glob, hashlib, urllib.request, urllib.parse
from concurrent.futures import ThreadPoolExecutor

BASE = "https://bavarswiss.ru/"
HERE = os.path.dirname(os.path.abspath(__file__))
OUT  = os.path.join(HERE, "site")
UA   = ("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/124.0 Safari/537.36")
LOCAL_HOSTS = {"bavarswiss.ru", "www.bavarswiss.ru", "i.siteapi.org", "s.siteapi.org"}
KNOWN_EXT = {"jpg","jpeg","png","gif","svg","webp","css","js","woff","woff2",
             "ttf","eot","ico","mp4","webm","json","avif"}
CT_EXT = {"image/jpeg":"jpg","image/png":"png","image/gif":"gif","image/svg+xml":"svg",
          "image/webp":"webp","image/avif":"avif","image/x-icon":"ico","image/vnd.microsoft.icon":"ico",
          "text/css":"css","application/javascript":"js","text/javascript":"js",
          "application/json":"json","font/woff2":"woff2","font/woff":"woff",
          "application/font-woff2":"woff2","application/font-woff":"woff",
          "font/ttf":"ttf","application/octet-stream":"bin","video/mp4":"mp4"}

def req(url):
    return urllib.request.Request(url, headers={"User-Agent": UA, "Referer": BASE})

def norm(u):
    u = u.strip()
    if not u or u.startswith(("data:", "#", "mailto:", "tel:", "javascript:")):
        return None
    if u.startswith("//"):   return "https:" + u
    if u.startswith("/"):    return "https://bavarswiss.ru" + u
    if u.startswith("http"): return u
    return None

def host_of(u): return urllib.parse.urlparse(u).netloc.lower()

def path_ext(u):
    p = urllib.parse.urlparse(u).path.lower()
    m = re.search(r'\.([a-z0-9]{2,5})$', p)
    return m.group(1) if m and m.group(1) in KNOWN_EXT else None

def rel_for(full, ctype):
    host = host_of(full).replace(":", "_")
    ext = path_ext(full)
    if not ext:
        ct = (ctype or "").split(";")[0].strip().lower()
        ext = CT_EXT.get(ct, "bin")
    h = hashlib.md5(full.encode("utf-8")).hexdigest()[:20]
    return f"assets/{host}/{h}.{ext}"

html = urllib.request.urlopen(req(BASE), timeout=45).read().decode("utf-8", "replace")
print("HTML bytes:", len(html))

refs = set()
for m in re.finditer(r'(?:src|href|data-src|data-original|data-bg|poster)\s*=\s*["\']([^"\']+)["\']', html, re.I):
    refs.add(m.group(1))
for m in re.finditer(r'srcset\s*=\s*["\']([^"\']+)["\']', html, re.I):
    for part in m.group(1).split(","):
        u = part.strip().split(" ")[0]
        if u: refs.add(u)
for m in re.finditer(r'url\(\s*["\']?([^"\')]+)["\']?\s*\)', html, re.I):
    refs.add(m.group(1))

SITEAPI = {"i.siteapi.org", "s.siteapi.org"}
def localizable(full):
    h = host_of(full)
    if h not in LOCAL_HOSTS:
        return False
    # на siteapi всё — ассеты; на самом домене — только файлы с расширением (не страницы вроде /video/..)
    return (h in SITEAPI) or (path_ext(full) is not None)

local = {ref: norm(ref) for ref in refs}
local = {ref: full for ref, full in local.items() if full and localizable(full)}
print("refs:", len(refs), "| to localize:", len(local))

def dl(item):
    ref, full = item
    h = hashlib.md5(full.encode("utf-8")).hexdigest()[:20]
    host = host_of(full).replace(":", "_")
    found = glob.glob(os.path.join(OUT, "assets", host, h + ".*"))
    if found:  # уже скачано — пропускаем (докачка)
        return (ref, os.path.relpath(found[0], OUT).replace("\\", "/"), os.path.getsize(found[0]), None)
    last = None
    for _ in range(3):
        try:
            with urllib.request.urlopen(req(full), timeout=60) as r:
                data = r.read()
                ctype = r.headers.get("Content-Type", "")
            rel = rel_for(full, ctype)
            dest = os.path.join(OUT, rel)
            os.makedirs(os.path.dirname(dest), exist_ok=True)
            with open(dest, "wb") as f: f.write(data)
            return (ref, rel, len(data), None)
        except Exception as e:
            last = repr(e)
    return (ref, None, 0, last)

with ThreadPoolExecutor(max_workers=10) as ex:
    results = list(ex.map(dl, local.items()))

ref2rel = {ref: rel for ref, rel, sz, err in results if rel}
new_html = html
# длинные ссылки заменяем первыми, чтобы не повредить те, что содержат их как префикс
for ref in sorted(ref2rel, key=len, reverse=True):
    new_html = new_html.replace(ref, ref2rel[ref])

# --- пост-обработка ---
# 1) noindex (превью не должно индексироваться, пока не запущено)
new_html = re.sub(r'(<head[^>]*>)',
                  r'\1\n<meta name="robots" content="noindex, nofollow">',
                  new_html, count=1)
# 2) карта: NetHouse-виджет -> самостоятельный Яндекс-виджет по адресу компании
_addr = "129626, Москва, 2-я Мытищинская улица, дом 2 строение 1"
_map = ('<iframe src="https://yandex.ru/map-widget/v1/?text=' + urllib.parse.quote(_addr) + '&z=17" '
        'id="ymap-1" style="width:100%;height:360px;margin-bottom:25px;" '
        'frameborder="0" allowfullscreen loading="lazy"></iframe>')
new_html = re.sub(r'<iframe src="https://bavarswiss\.ru/contacts/showmap[^>]*>\s*</iframe>',
                  _map, new_html, count=1)

os.makedirs(OUT, exist_ok=True)
with open(os.path.join(OUT, "index.html"), "w", encoding="utf-8") as f:
    f.write(new_html)

ok   = [r for r in results if r[1]]
fail = [r for r in results if r[3]]
print("downloaded:", len(ok), "| failed:", len(fail), "| bytes:", sum(r[2] for r in ok))
for r in fail[:25]:
    print("  FAIL", r[0][:80], "->", r[3])
