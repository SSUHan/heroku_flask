from app import app
from flask import jsonify, render_template, request, redirect, flash, url_for
from app import db
from app.model.user import User
from app.job.user import find_user_by_id, check_admin, do_join, do_login
from app.config.appConfig import PreDefine


@app.route('/')
def index():
	return "hello im junsu"

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
	#print(queries.user_id)

	entries = [dict(user_id=user.user_id, user_name=user.user_name, point=user.point, created=user.created) for user in queries]
	#print(jsonify(entries))
	print(entries)

	return render_template('show_all.html', users=queries)

# 회원 가입
@app.route('/su_point/add_user', methods=['GET', 'POST'])
def add_user():
	
	if request.method == 'POST':
		# web 에서 로그인할 경우에
		if request.form[PreDefine.source] == PreDefine.source_web:
			# TODO : 어드민 계정으로 들어왓는지를 먼저 확인해야함
			print("in web")
			result = do_join(request)
			if result == -1:
				flash('Please enter all the fields', 'error')
			elif result == 0:
				flash('This ID already exist in server', 'fail')
			elif result == 1:
				flash('Record was successfully added!', 'success')
				return redirect(url_for('show_users_by_admin'))
		elif request.form[PreDefine.source] == PreDefine.source_mobile:
			print("in mobile")
			result = do_join(request)
			to_client = dict()
			if result == -1:
				to_client['join'] = False
				to_client['message'] = "필요한 정보가 모두 입력되지 않았습니다"
			elif result == 0:
				to_client['join'] = False
				to_client['message'] = "중복된 아이디가 존재합니다"
			elif result == 1:
				to_client['join'] = True
				to_client['message'] = "회원가입에 성공하였습니다"
			return jsonify(to_client)

	return render_template('add_user.html')

@app.route('/su_point/login', methods=['POST'])
def login_user():
	if request.method == 'POST':
		if request.form[PreDefine.source] == PreDefine.source_mobile:
			to_client = dict()
			to_client['login'], to_client['message'], user = do_login(request)
			if user:
				to_client['permission'] = user.permission

		return jsonify(to_client)
	return "login html would be placed"

@app.route('/test', methods=['GET', 'POST'])
def test():
	if check_admin(request.form['user_id']):
		return "yes admin"
	else:
		return "no admin"

@app.route('/test/gcm_test', methods=['POST'])
def gcm_test():
	from gcm import GCM
	from gcm.gcm import GCMException
	sender = GCM('AIzaSyA9jPIJgBlHOa3g7nVaYTNBGK1V24Lpo14')
	reg_id = list()
	reg_id.append(request.form['reg_id'])
	title = request.form['title']
	message = request.form['message']
	noti_type = request.form['notification_type']
	data = {'title': title, 'message': message, 'notification_type': noti_type}

	result = sender.json_request(registration_ids=reg_id, data=data)

	print(result)
	return jsonify(request)

