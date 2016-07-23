from app import db
from datetime import datetime

class Friend(db.Model):
    __tablename__ = 'su_friend'

    fpk = db.Column(db.Integer, primary_key=True, autoincrement=True)
    user_id = db.Column(db.String(30))
    friend_id = db.Column(db.String(30))
    created = db.Column(db.DateTime)

    def __init__(self, user_id, friend_id):
        self.user_id = user_id
        self.friend_id = friend_id
        self.created = datetime.now()
