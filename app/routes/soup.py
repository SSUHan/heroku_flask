from app import app
from app.job.soup.ssu_main_page import ssu_main_page_parsing
from flask import jsonify

@app.route('/soup/')
def soup_index():
    return "Page Parsing Index Page"

@app.route('/soup/ssu_main_page')
def ssu_main_page():
    ssu_main_notice_list =  ssu_main_page_parsing()
    return jsonify(ssu_main_notice_list)