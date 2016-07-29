from app.model.su_point.user import User
from sqlalchemy import desc

def find_local_ranking_list():
    queries = User.query \
        .filter_by(permission='normal') \
        .order_by(desc(User.point)) \
        .limit(10) \
        .all()

    entries = [user.to_dict() for user in
               queries]

    return entries
