#!/usr/bin/python
# -*- coding: utf-8 -*-
from flask.ext.script import Manager
# from flask.ext.migrate import MigrateCommand
from app import create_app
from app.database import DBManager
#
import sys
reload(sys)
sys.setdefaultencoding("utf-8")

app = create_app()
manager = Manager(app)
# manager.add_command('db', MigrateCommand)

def checkAction(message):
    input = raw_input(message)
    if input == "Y" :
        return True
    return False

# 실행방법
# python manage.py init_db
@manager.command
def init_db():
    if checkAction("Are you sure to init DB ? (Y/n) ") == False:
        return
    with app.app_context():
        DBManager.init_db()

# 실행방법
# python manage.py clear_db
@manager.command
def clear_db():
    if checkAction("Are you sure to clear DB ? (Y/n) ") == False:
        return
    with app.app_context():
        DBManager.clear_db()

if __name__ == '__main__':
    manager.run()
