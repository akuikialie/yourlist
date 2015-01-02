## AUTH API Documentation

- [Login User](#login-user)
    + [Resource URL](#login-user-url)
    + [Parameters](#login-user-param)
    + [Sample Request](#login-user-request)
    + [Sample Response Success](#login-user-response-success)
    + [Sample Response Failed](#login-user-response-failed)
- [Register User](#register-user)
    + [Resource URL](#register-user-url)
    + [Parameters](#register-user-param)
    + [Sample Request](#register-user-request)
    + [Sample Response Success](#register-user-response-success)
    + [Sample Response Failed](#register-user-response-failed)



#### <a name="login-user"></a> `🔒` `POST` Login User
Login your Account

##### <a name="login-user-url"></a> Resource URL
/api/auth/login

##### <a name="login-user-param"></a> Parameters
+ `username` _`required`_ Username.
+ `password` _`required`_ Password.

##### <a name="login-user-request"></a>Sample Request
````sh
curl http://110.5.109.170/depo/yourlist/api/auth/login
````

##### <a name="get-active-user-response-success"></a>Sample Response Success

````json
{
    "status": 1,
    "data": {
        "id_user": "1",
        "username": "akuikialie",
        "email": null,
        "phone": null,
        "id_level": "3",
        "id_status": "1",
        "activate": "1",
        "deleted": "0",
        "created": "2014-12-27 11:38:33",
        "creator": "1",
        "changed": null,
        "changer": null,
        "avatar": null,
        "gcm_id": null,
        "id_key": "2",
        "key": "5e75f8d997f481581250ad56c7f23e2f9ede75af"
    }
}
````
##### <a name="get-active-user-response-failed"></a>Sample Response Failed

````json
{
    "status": 0,
    "error": "Your Account is not Valid"
}
````


#### <a name="register-user"></a> `🔒` `POST` Register User
Register your Account

##### <a name="register-user-url"></a> Resource URL
/api/auth/register

##### <a name="login-user-param"></a> Parameters
+ `username` _`required`_ Username.
+ `password` _`required`_ Password.
+ `confirm_password` _`required`_ Confirm Password 

##### <a name="register-user-request"></a>Sample Request
````sh
curl http://110.5.109.170/depo/yourlist/api/auth/register
````

##### <a name="register-user-response-success"></a>Sample Response Success

````json
{
    "status": 1,
    "message": "Register Success"
}
````
##### <a name="register-user-response-failed"></a>Sample Response Failed

````json
{
    "status": 0,
    "error": "Your Account is not Valid"
}
````
