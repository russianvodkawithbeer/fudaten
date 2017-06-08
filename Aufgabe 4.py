import requests
import re
from bs4 import BeautifulSoup
from collections import Counter
import string


def link(url):
    html = requests.get(url).text
    soup = BeautifulSoup(html, "html.parser")
    zaehler = Counter()
    for bible_text in soup.findAll('header'):
        text = re.sub(r"(?m)^[^\S\n]+", "", bible_text.get_text().lower())
        zaehler.update(text.split(" "))
    return zaehler

zaehler1 = link('https://www.heise.de/thema/https')

print(zaehler1.most_common(3))
