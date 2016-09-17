from bs4 import BeautifulSoup
from urllib.request import urlopen
import json


def ssu_main_page_parsing():

    res = urlopen('http://www.ssu.ac.kr/web/kor/plaza_d_01').read().decode('utf-8')

    # print(res)
    soup = BeautifulSoup(res, 'html.parser')

    # print(soup.prettify()) # soup 내용물 이쁘게 보기
    # print(type(soup))      # soup

    notice_table = soup.find('table', class_='bbs-list') # table 찾기

    # print(notice_table.prettify())  # Tag 내용물 이쁘게 보기
    # print(type(notice_table))       # element.Tag

    notice_tr_list = soup.find_all('tr', class_='trNotice') # trNotice class 의 tr 태그들 찾기

    to_client_list = list()
    index = 1
    for notice_item in notice_tr_list:
        # print("="*10, index, "="*10)
        each_item = dict()
        # print(type(notice_item))        # element.Tag
        # print(notice_item.prettify())
        notice_td_item = notice_item.find_all('td')  # 메모리 효율을 위해서 따로 뺀다

        each_item['index'] = index

        # each_item['title'] = notice_item.find('td', class_='left bold').a.string #  Tag 객체 안에서도 find 를 쓸 수 있다
        # each_item['link'] = notice_item.find('td', class_='left bold').a['href']

        each_item['title'] = notice_td_item[1].a.string
        each_item['link'] = notice_td_item[1].a['href']
        each_item['datetime'] = notice_td_item[4].string
        to_client_list.append(each_item)
        index += 1

    return to_client_list