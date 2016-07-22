from app import db
from app.model.user import User

def find_user(user_id):
    user = User.query.filter_by(user_id=user_id).first()
    return user