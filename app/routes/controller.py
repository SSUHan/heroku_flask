from app import app, db
from flask import jsonify, render_template, request, redirect, flash, url_for
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
		value = request.get_json()
		return value
		# web 에서 로그인할 경우에
		if request.form[PreDefine.source] == PreDefine.source_web:
		#if request.form['source'] == "web":
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
		#elif request.form['source'] == "mobile":
			#print("in mobile")
			result = do_join(request)
			#print("after join")
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

@app.route('/su_point/add_friend', methods=['POST'])
def add_friend():
	from app.job.friend import add_friend
	to_client = dict()
	to_client['add_friend'], to_client['message'] = add_friend(request.form['user_id'], request.form['friend_id'])
	return jsonify(to_client)


@app.route('/su_point/find_friends', methods=['POST'])
def find_friends():
	from app.job.friend import find_friends

	friends = find_friends(request.form['user_id'])

	to_client = dict()
	if friends:
		to_client['find_friends'] = True
		to_client['friend_list'] = list()
		for item in friends:
			user_info = find_user_by_id(item.friend_id)
			new_item = dict()
			new_item['user_id'] = item.user_id
			new_item['friend_id'] = item.friend_id
			new_item['friend_name'] = user_info.user_name
			new_item['point'] = user_info.point
			to_client['friend_list'].append(new_item)

		print(to_client)

	else:
		to_client['find_friends'] = False
	return jsonify(to_client)

@app.route('/test', methods=['GET', 'POST'])
def test():
	if check_admin(request.form['user_id']):
		return "yes admin"
	else:
		return "no admin"

@app.route('/test/gcm_test', methods=['POST'])
def gcm_test():
	from app.job.gcm import push_gcm_message

	reg_ids = list()
	reg_ids.append(request.form['reg_id'])

	result = push_gcm_message(reg_ids, request.form['title'], request.form['message'], request.form['notification_type'])
	print(result)
	try:
		print(result['success'])
		print(result['error'])
	except Exception:
		print("in exception")
	return jsonify(result)

