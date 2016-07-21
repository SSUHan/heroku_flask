from app import app
from flask import jsonify, render_template, request
from app import db
from app.model.user import User


@app.route('/')
def index():
	return render_template('index.html')

@app.route('/<name>')
def index_into(name):
	return render_template(name)

@app.route('/data')
def data():
	data = {"names": ["John", "Jacob", "Julie", "Jennifer"]}
	return jsonify(data)

# @app.route('/su_point/db_insert', methods=["GET"])
# def db_insert():
# 	user_id = request.args.get('user_id')
# 	user_name = request.args.get('user_name')
# 	point = request.args.get('point')
#
# 	person = User(user_id, user_name, point)
# 	db.session.add(person)
# 	db.session.commit()
#
# 	output = User.query.all()
# 	string = ""
# 	for item in output:
# 		string = string + item.user_id + " " + item.user_name + "<br>"
# 	return string