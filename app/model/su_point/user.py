#!/usr/bin/python
# -*- coding: utf-8 -*-
from app import db
from datetime import datetime


class User(db.Model):
    __tablename__ = 'su_user'
    user_id = db.Column(db.String(30), primary_key=True, unique=True)
    user_name = db.Column(db.String(30))
    point = db.Column(db.Integer)
    permission = db.Column(db.String(20))
    user_pw = db.Column(db.String(30))
    created = db.Column(db.DateTime)

    def __init__(self, user_id, user_name, point, permission, user_pw):
        self.user_id = user_id
        if user_name:
            self.user_name = user_name
        else:
            self.user_name = '사용자'
        if point:
            self.point = point
        else:
            self.point = 0
        if permission:
            self.permission = permission
        else:
            self.permission = 'normal'
        self.user_pw = user_pw
        self.created = datetime.now()

    def __repr__(self):
        return '{user_id : "%s", user_name : "%s", point : "%d"}' %\
               {self.user_id, self.user_name, self.point}

    def to_dict(self):
        return dict(user_id=self.user_id, user_name=self.user_name, point=self.point, permission=self.permission, created=self.created)