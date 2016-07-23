#!/usr/bin/python
# -*- coding: utf-8 -*-
from app import db
from datetime import datetime

class Gcm(db.Model):
    __tablename__ = 'su_gcm'
    user_id = db.Column(db.String(20), primary_key=True)
    gcm_token = db.Column(db.Text)

    def __init__(self, user_id, gcm_token):
        self.user_id = user_id
        self.gcm_token = gcm_token

