#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Генератор ACF-полей главной BavarSwiss.
Извлекает редактируемые тексты из main.html и пишет inc/main-fields.php
(массив field => [label, default]). Также нормализует &nbsp; -> U+00A0,
чтобы в админке не отображалась сущность '&nbsp;'.
Запуск из папки темы: python build-fields.py
"""
import os, re, json

HERE = os.path.dirname(os.path.abspath(__file__))
MAIN = os.path.join(HERE, "main.html")
OUT  = os.path.join(HERE, "inc", "main-fields.php")

# Поля первой партии: (name, label, regex с одной группой захвата текста).
FIELDS = [
    ("main_hero_title", "Hero · заголовок",            r'>(БИОТЕХНОЛОГИИ[^<]*?)\s*<'),
    ("main_cap1", "Кружок 1 · подпись",                r'>(Разработки технологий[^<]*?)\s*<'),
    ("main_cap2", "Кружок 2 · подпись",                r'>(Разработки пилотного[^<]*?)\s*<'),
    ("main_cap3", "Кружок 3 · подпись",                r'>(Разработка и аудит[^<]*?)\s*<'),
    ("main_cap4", "Кружок 4 · подпись",                r'>(Технологическое проектирование, комплектация[^<]*?)\s*<'),
    ("main_cap5", "Кружок 5 · подпись",                r'>(Проектирование и оснащение[^<]*?)\s*<'),
    ("main_h_podkluch",  "Заголовок · Промышленная микробиология", r'>(ПРОМЫШЛЕННАЯ МИКРОБИОЛОГИЯ[^<]*?)\s*<'),
    ("main_h_equipment", "Заголовок · Технологическое оборудование", r'>(ТЕХНОЛОГИЧЕСКОЕ ОБОРУДОВАНИЕ)\s*<'),
    ("main_h_ferment",   "Подзаголовок · ферментёры", r'>(Лабораторные, пилотные[^<]*?)\s*<'),
    ("main_h_contacts",  "Заголовок · Контактная информация", r'>(Контактная информация)\s*<'),
    ("main_addr_ru", "Контакты · адрес (РФ)",          r'>(129626[^<]*?)\s*<'),
    ("main_addr_ch", "Контакты · адрес (Швейцария)",   r'>(Kasernenstrasse[^<]*?)\s*<'),
    ("main_about", "Заголовок · О компании",           r'>(О КОМПАНИИ)\s*<'),
    ("main_p_microbio", "Текст · Промышленная микробиология (вступление)", r'>(Промышленная микробиология представляет[^<]*?)\s*<'),
]

html = open(MAIN, encoding="utf-8").read()

# нормализуем неразрывный пробел
before = html.count("&nbsp;")
html = html.replace("&nbsp;", " ")
open(MAIN, "w", encoding="utf-8").write(html)

rows, report = [], []
for name, label, pat in FIELDS:
    m = re.search(pat, html)
    if not m:
        report.append((name, "NOT FOUND", 0))
        continue
    text = m.group(1).strip()
    rows.append((name, label, text))
    report.append((name, "ok", len(text)))

def php_q(s):
    return s.replace("\\", "\\\\").replace("'", "\\'")

with open(OUT, "w", encoding="utf-8") as f:
    f.write("<?php\n/** Сгенерировано build-fields.py — поля и дефолты главной BavarSwiss. */\n")
    f.write("if (!defined('ABSPATH')) exit;\nreturn [\n")
    for name, label, text in rows:
        f.write("    '%s' => ['label' => '%s', 'default' => '%s'],\n"
                % (name, php_q(label), php_q(text)))
    f.write("];\n")

print("nbsp normalized:", before)
print("fields written:", len(rows), "->", OUT)
for name, status, n in report:
    print(f"  {name}: {status} ({n} chars)")
