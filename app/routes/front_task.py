from app import app
from flask import render_template
@app.route('/front_task/elepartsMain')
def eleparts_main():
    return render_template('/front_task/elepartsMain.htm')