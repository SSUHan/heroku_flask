from app.model.user import User
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
    if not user_id or not request.form['user_name'] or not request.form['permission']:
        # flash('Please enter all the fields', 'error')
        return -1
    else:
        user = find_user_by_id(user_id)
        if not user:
            user = User(user_id, request.form['user_name'], request.form['point'], request.form['permission'])
            db.session.add(user)
            db.session.commit()

            # flash('Record was successfully added!', 'success')
            # return redirect(url_for('show_users_by_admin'))
            return 1
        else:
            # flash('This ID already exist in server', 'fail')
            return 0
