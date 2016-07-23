#!/usr/bin/python
# -*- coding: utf-8 -*-
import os


class AppConfig(object):
    SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://root:@localhost/mytest?charset=utf8'
    SQLALCHEMY_ECHO = True
    SQLALCHEMY_TRACK_MODIFICATIONS = True

class PreDefine:
    # input client source
    source = 'source'
    source_mobile = 'mobile'
    source_web = 'web'
