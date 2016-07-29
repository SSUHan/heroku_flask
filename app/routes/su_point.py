from app import app, db
from flask import jsonify, render_template, request, redirect, flash, url_for
from app.job.su_point.user import find_user_by_id, check_admin, do_join, do_login
from app.model.su_point.user import User
from app.config.appConfig import PreDefine

# db insert test code
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

@app.route('/su_point/admin_login')
def admin_login():
	return render_template('login.html')

@app.route('/su_point/show_users', methods=['GET', 'POST'])
def show_users_by_admin():
	if request.method == 'POST':
		is_user = do_login(request)
		is_admin = check_admin(request.form['user_id'])
		if is_user and is_admin:
			queries = User.query.all()
			#print(queries.user_id)

			entries = [dict(user_id=user.user_id, user_name=user.user_name, point=user.point, created=user.created) for user in queries]
			#print(jsonify(entries))
			print(entries)

			return render_template('show_all.html', users=queries)
		else:
			return render_template('login.html')
	return render_template('page_locked.html')

# 회원 가입
@app.route('/su_point/add_user', methods=['GET', 'POST'])
def add_user():
	from app.job.su_point.user import do_join_in_mobile
	if request.method == 'POST':
		value = request.json
		to_client = dict()
		if value:
			result = do_join_in_mobile(value)
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
			# web 에서 로그인할 경우에
		elif request.form[PreDefine.source] == PreDefine.source_web:
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




	return render_template('add_user.html')

@app.route('/su_point/login', methods=['POST'])
def login_user():
	from app.job.su_point.user import do_login_by_mobile
	if request.method == 'POST':
		value = request.json
		to_client = dict()
		if value:
			to_client['login'], to_client['message'], user = do_login_by_mobile(value)
			if user:
				to_client['permission'] = user.permission
			return jsonify(to_client)
		elif request.form[PreDefine.source] == PreDefine.source_mobile:
			to_client['login'], to_client['message'], user = do_login(request)
			if user:
				to_client['permission'] = user.permission

		return jsonify(to_client)
	return "login html would be placed"

@app.route('/su_point/add_friend', methods=['POST'])
def add_friend():
	from app.job.su_point.friend import add_friend
	value = request.json
	to_client = dict()
	if value:
		to_client['add_friend'], to_client['message'] = add_friend(value['user_id'], value['friend_id'])
	else:
		to_client['add_friend'], to_client['message'] = add_friend(request.form['user_id'], request.form['friend_id'])
	return jsonify(to_client)


@app.route('/su_point/find_friends', methods=['POST'])
def find_friends():
	from app.job.su_point.friend import find_friends
	value = request.json
	friends = find_friends(value['user_id'])
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

@app.route('/su_point/show_ranking', methods=['GET', 'POST'])
def show_rankings():
	from app.job.su_point.ranking import find_local_ranking_list
	user_ranks = find_local_ranking_list()
	entries = [dict(user_id=user.user_id, user_name=user.user_name, point=user.point, created=user.created) for user in
			   user_ranks]

	return jsonify(entries)
