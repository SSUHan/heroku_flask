from app import app
from flask import jsonify, render_template, request, redirect, flash, url_for
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

@app.route('/su_point/db_insert', methods=["GET"])
def db_insert():
	user_id = request.args.get('user_id')
	user_name = request.args.get('user_name')
	point = request.args.get('point')

	person = User(user_id, user_name, point)
	db.session.add(person)
	db.session.commit()

	output = User.query.all()
	string = ""
	for item in output:
		string = string + item.user_id + " " + item.user_name + "<br>"
	return string

@app.route('/su_point/show_users')
def show_users_by_admin():
	queries = User.query.all()

	entries = [dict(user_id=user.user_id, user_name=user.user_name, point=user.point, created=user.created) for user in queries]
	print(entries)

	return render_template('show_all.html', users=queries)

@app.route('/su_point/add_user', methods=['GET', 'POST'])
def add_user_by_admin():
	# TODO : 어드민 계정으로 들어왓는지를 먼저 확인해야함
	if request.method == 'POST':
		if not request.form['user_id'] or not request.form['user_name']:
			flash('Please enter all the fields', 'error')
		else:
			user = User(request.form['user_id'], request.form['user_name'], request.form['point'])
			db.session.add(user)
			db.session.commit()

			flash('Record was successfully added!')
			return redirect(url_for('show_users_by_admin'))
	return render_template('add_user.html')