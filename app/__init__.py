from flask import Flask
from flask_sqlalchemy import SQLAlchemy

app = Flask(__name__)
# When AWS
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:mysql-root@localhost/mytest?charset=utf8'
# When Local
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/mytest?charset=utf8'
# When heroku server
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://cyavulucaoc6n4mo:zsdd5b5zm0mcd2j4@nj5rh9gto1v5n05t.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/d6wpcbemofr48xfa'
app.config['SQLALCHEMY_ECHO'] = True
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = True
app.secret_key = 'manyrandombyte'



db = SQLAlchemy(app)

from app.restless import initRestlessApi
initRestlessApi(app)

from app.blueprint import basic
app.register_blueprint(basic)


from app.model import *
from app.routes import *
from app.config import *

db.create_all()

