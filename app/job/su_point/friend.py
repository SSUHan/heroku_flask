#!/usr/bin/python
# -*- coding: utf-8 -*-
from app import db
from app.model.su_point.friend import Friend
from sqlalchemy import and_
from app.job.su_point.user import find_user_by_id

# 친구 목록을 리턴하는 함수이다
# first 옵션을 부여하면 첫번쩨만 리턴한다
def find_friends(user_id, first=False):
    queries = Friend.query.filter(Friend.user_id == user_id)
    if first:
        return queries.first()
    else:
        return queries.all()

def already_friend(user_id, friend_id):
    queries = Friend.query.filter(
        and_(
            Friend.user_id == user_id,
            Friend.friend_id == friend_id)
    ).first()
    if queries:
        return True
    else:
        return False

def add_friend(user_id, friend_id):
    user = find_user_by_id(user_id)
    friend = find_user_by_id(friend_id)
    if user and friend:
        # 친구 아이디가 정상적으로 존재해야 의미가 있다
        is_friend = already_friend(user_id, friend_id)
        if not is_friend:
            # 친구가 아닐때 친구추가를 해야한다
            new_friend = Friend(user_id, friend_id)
            db.session.add(new_friend)
            db.session.commit()
            return True, "친구추가에 성공하였습니다"
        else:
            return False, "이미 친구로 등록되어 있습니다"
    else:
        return False, "존재하지 않는 아이디 입니다"
