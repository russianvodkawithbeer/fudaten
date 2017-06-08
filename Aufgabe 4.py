import requests
import re
from bs4 import BeautifulSoup
from collections import Counter
import string


def link(url):
    html = requests.get(url).text
    soup = BeautifulSoup(html, "html.parser")
    #legen zähler fest
    zaehler = Counter()
    for bible_text in soup.findAll('header'):
        #löscht paar unnötige sachen
        text = re.sub(r"(?m)^[^\S\n]+", "", bible_text.get_text().lower())
        zaehler.update(text.split(" "))
    return zaehler

# Der link aus der Aufgabe
zaehler1 = link('https://www.heise.de/thema/https')

#Ausgabe
print(zaehler1.most_common(3))
