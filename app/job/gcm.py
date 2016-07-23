#!/usr/bin/python
# -*- coding: utf-8 -*-

from gcm import GCM

SERVER_API_KEY = 'AIzaSyA9jPIJgBlHOa3g7nVaYTNBGK1V24Lpo14'
# TODO: ids 는 리스트 형식이어야 합니다
def push_gcm_message(ids, title, message, notification_type=True):
    sender = GCM(SERVER_API_KEY)
    #reg_id = list()
    #reg_id.append(request.form['reg_id'])
    data = {'title': title, 'message': message, 'notification_type': notification_type}

    result = sender.json_request(registration_ids=ids, data=data)
    return result