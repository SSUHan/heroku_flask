from app import app
from flask import request, jsonify

@app.route('/slack/', methods=['GET', 'POST'])
def slack_index():

    if request.method == 'GET':
        # print(request.form['user_id'])
        return jsonify(request.form)
    elif request.method == 'POST':
        value = request.json # value 는 dict 이다
        print(value)
        return jsonify(value)