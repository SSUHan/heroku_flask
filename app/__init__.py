from flask import Flask

def create_app():
    app = Flask(__name__)
    #init routes
    from app.routes import controller

    from app.blueprint import basic
    app.register_blueprint(basic)
    return app

