from app.blueprint import basic
from flask import jsonify, render_template

@basic.route('/')
def index():
	return render_template('index.html')

@basic.route('/data')
def data():
	data = {"names": ["John", "Jacob", "Julie", "Jennifer"]}
	return jsonify(data)