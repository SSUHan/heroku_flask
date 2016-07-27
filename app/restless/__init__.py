from app.database import DBManager
from app.model.su_point.user import User
from flask_restless import APIManager

def initRestlessApi(app):
    manager = APIManager(app, flask_sqlalchemy_db=DBManager.db)
    manager.create_api(User, methods=['GET', 'POST', 'DELETE'])