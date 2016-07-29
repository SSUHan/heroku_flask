from app.model.su_point.user import User
from sqlalchemy import desc

def find_local_ranking_list():
    rank_users = User.query \
        .filter_by(permission='normal') \
        .order_by(desc(User.point)).all()

    return rank_users
