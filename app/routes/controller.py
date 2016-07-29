from app import app, db
from flask import jsonify, render_template, request, redirect, flash, url_for
from app.model.su_point.user import User
from app.job.su_point.user import check_admin

@app.route('/')
def index():
	return render_template('index.html')

@app.route('/<name>')
def index_into(name):
	return render_template(name)

@app.errorhandler(404)
def page_not_found(e):
	app.logger.error('Page not found %s', (request.path))
	return "this page does not exist", 404

@app.errorhandler(500)
def page_server_error(e):
	return render_template('page_500_option2.html'), 500

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


@app.route('/test', methods=['GET', 'POST'])
def test():
	if check_admin(request.form['user_id']):
		return "yes admin"
	else:
		return "no admin"

@app.route('/test/gcm_test', methods=['POST'])
def gcm_test():
	from app.job.su_point.gcm import push_gcm_message
	value = request.json
	reg_ids = list()
	if value:
		#reg_ids.append(value['friend_id'])
		reg_ids.append('d_WAsnGB_RA:APA91bFzIAokbPGpfVM_wjTLcfVaC_ElQLCkaT4HjqKLqHd2ZR8juIneBw2uBvM3i3MFS5N9VFyidFE-ar4mEifTFC1cuyk8q4QyChIHvOoyIkzK9e002m15tk0MkCg6ju1E_dJRxFwj')
		result = push_gcm_message(reg_ids, "SuPoint", "열심히좀 하자 친구야!", True)
	else:
		reg_ids.append(request.form['reg_id'])
		result = push_gcm_message(reg_ids, request.form['title'], request.form['message'], request.form['notification_type'])
	print(result)
	try:
		print(result['success'])
		print(result['error'])
	except Exception:
		print("in exception")
	return jsonify(result)

