from flask import Blueprint

basic = Blueprint('basic', __name__,
                  template_folder='templates/su_point',
                  static_url_path='/assets',
                  static_folder='assets')
