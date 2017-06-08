from bs4 import BeautifulSoup
import requests

result = requests.get("http://www.bbc.com/news")

soup = BeautifulSoup(result.content, "lxml")

headlines = soup.find_all("h3")[:5]

for headline in headlines:
    print (headline.text)
