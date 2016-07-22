from app.model.user import User

def find_user(user_id):
    user = User.query.filter_by(user_id=user_id).first()
    return user

def check_admin(user_id):
    user = find_user(user_id)
    if user.permission == 'admin':
        return True
    else:
        return False