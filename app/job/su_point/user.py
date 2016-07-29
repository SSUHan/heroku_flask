from app.model.su_point.user import User
from app import db

# 유저 찾기
def find_user_by_id(user_id):
    user = User.query.filter_by(user_id=user_id).first()
    return user

def check_admin(user_id):
    user = find_user_by_id(user_id)
    if user.permission == 'admin':
        return True
    else:
        return False


def do_join(request):
    user_id = request.form['user_id']
    if not user_id or not request.form['user_name'] or not request.form['permission'] or not request.form['user_pw']:
        # flash('Please enter all the fields', 'error')
        return -1
    else:
        user = find_user_by_id(user_id)
        if not user:
            user = User(user_id,
                        request.form['user_name'],
                        request.form['point'],
                        request.form['permission'],
                        request.form['user_pw'])
            db.session.add(user)
            db.session.commit()

            # flash('Record was successfully added!', 'success')
            # return redirect(url_for('show_users_by_admin'))
            return 1
        else:
            # flash('This ID already exist in server', 'fail')
            return 0
def do_join_in_mobile(value):
    user_id = value['user_id']
    if not user_id or not value['user_name'] or not value['permission'] or not value['user_pw']:
        # flash('Please enter all the fields', 'error')
        return -1
    else:
        user = find_user_by_id(user_id)
        if not user:
            user = User(user_id,
                        value['user_name'],
                        value['point'],
                        value['permission'],
                        value['user_pw'])
            db.session.add(user)
            db.session.commit()

            # flash('Record was successfully added!', 'success')
            # return redirect(url_for('show_users_by_admin'))
            return 1
        else:
            # flash('This ID already exist in server', 'fail')
            return 0

# 로그인
def do_login(request):
    user = find_user_by_id(request.form['user_id'])
    if not user:
        return False, "아이디가 일치하지 않습니다", None
    else:
        if user.user_pw == request.form['user_pw']:
            return True, "인증에 성공하였습니다", user
        else:
            return False, "비밀번호가 일치하지 않습니다", None

def do_login_by_mobile(value):
    user = find_user_by_id(value['user_id'])
    if not user:
        return False, "아이디가 일치하지 않습니다", None
    else:
        if user.user_pw == value['user_pw']:
            return True, "인증에 성공하였습니다", user
        else:
            return False, "비밀번호가 일치하지 않습니다", None
