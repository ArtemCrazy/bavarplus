#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Делает видео-галерею автономной: ссылки NetHouse /video/NNN заменяются на
открытие YouTube-ролика в лайтбоксе (ID берётся из превью img.youtube.com/vi/<ID>).
Фолбэк без JS — ссылка на youtube.com в новой вкладке. Идемпотентно.
Запуск из папки темы: python build-video.py
"""
import os, re

HERE = os.path.dirname(os.path.abspath(__file__))
MAIN = os.path.join(HERE, "main.html")
html = open(MAIN, encoding="utf-8").read()

block = re.compile(r'(<a) data-video-href="/video/\d+" href="#video-\d+"(>)(.*?)(</a>)', re.S)

def repl(m):
    inner = m.group(3)
    yt = re.search(r'img\.youtube\.com/vi/([\w-]+)/', inner)
    if not yt:
        return m.group(0)
    vid = yt.group(1)
    open_tag = ('%s class="bavar-video" data-yt="%s" '
                'href="https://www.youtube.com/watch?v=%s" target="_blank" rel="noopener"%s'
                % (m.group(1), vid, vid, m.group(2)))
    return open_tag + inner + m.group(4)

html, n = block.subn(repl, html)

LB = """
<div id="bv-lb" aria-hidden="true"></div>
<style>
#bv-lb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:99999;align-items:center;justify-content:center}
#bv-lb .bv-in{position:relative;width:90%;max-width:900px}
#bv-lb .bv-fr{position:relative;padding-bottom:56.25%;height:0}
#bv-lb .bv-fr iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
#bv-lb .bv-x{position:absolute;top:-42px;right:0;background:none;border:0;color:#fff;font-size:36px;line-height:1;cursor:pointer}
</style>
<script>
(function(){
  document.addEventListener('click',function(e){
    var a=e.target.closest&&e.target.closest('a.bavar-video');
    if(!a)return; var id=a.getAttribute('data-yt'); if(!id)return;
    e.preventDefault();
    var o=document.getElementById('bv-lb');
    o.innerHTML='<div class="bv-in"><button class="bv-x" aria-label="Закрыть">&times;</button>'+
      '<div class="bv-fr"><iframe src="https://www.youtube.com/embed/'+id+'?autoplay=1" allow="autoplay; fullscreen" allowfullscreen></iframe></div></div>';
    o.style.display='flex';
  });
  document.addEventListener('click',function(e){
    var o=document.getElementById('bv-lb'); if(!o||o.style.display!=='flex')return;
    if(e.target===o||(e.target.classList&&e.target.classList.contains('bv-x'))){o.style.display='none';o.innerHTML='';}
  });
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){var o=document.getElementById('bv-lb'); if(o){o.style.display='none';o.innerHTML='';}}
  });
})();
</script>
"""

if 'id="bv-lb"' not in open(MAIN, encoding="utf-8").read():
    html = html.replace("</body>", LB + "\n</body>", 1)

open(MAIN, "w", encoding="utf-8").write(html)
print("video links rewritten:", n)
print("lightbox injected:", 'id="bv-lb"' in html)
