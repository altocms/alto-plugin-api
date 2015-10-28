# Alto CMS Restful API - resource USERS

## Authorization

### `| POST /users/login `
Авторизация пользователя по переданному емейлу или логину

_Требуется авторизация: нет_

### Parameters
* email - емейл пользователя (может быть опущен, если передан логин)
* login - логин пользователя (может быть опущен, если передан емейл)
* password - пароль пользователя

### Return data
* auth_token - токен авторизации (если регистрация прошла успешно)
* user - информация о пользователе (если регистрация прошла успешно)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        auth_token: fedcba987654321,
        user: { <user_data> }
    }
}
```
По умолчанию **user_data** это следующий набор данных:
 * id        (int)
 * login     (string)
 * name      (string)
 * sex       (string)
 * avatar    (url)
 * photo     (url)
 * about     (string)
 * birthday  (date)
 * skill     (float)
 * rating    (float)
 * profile   (url)
 * country   (string)
 * city      (string)
 * region    (string)
 * is_online (bool)
 * is_friend (bool)


## Logout

### `| POST /users/logout `
Выход пользователя (отмена авторизации)

_Требуется авторизация: да_

## Get list of users

### `| GET /users/list?<parameters> `
Получить список пользователей (с разбивкой на страницы)

### Parameters
* page (optional) - номер страницы (по умолчанию = 1)
* page_size (optional) - размер страницы (по умолчанию = 25)

### Return data:
* auth_token - токен авторизации (если пользователь авторизован)
* users - коллекция пользователей
    * total - общее число пользователей
    * list - список пользователей (массив)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        users: {
            total: 1234,
            list: [
                 {<user_data_1>},
                 {<user_data_2>},
                 ...
            ]
        }
    }
}
```
## Get self data

### `| GET /users/me `
Возвращает данные авторизованного пользователя

_Требуется авторизация: да_

### Return data
* auth_token - токен авторизации (если пользователь авторизован)
* user - информация о пользователе

### Example response
```
{
    error: 0,
    message: '',
    data: {
        auth_token: fedcba987654321,
        user: { <user_data> }
    }
}
```
## Get required user

### `| GET /users/:id `
Возвращает данные авторизованного пользователя

_Требуется авторизация: нет_

### Return data
* auth_token - токен авторизации (еесли пользователь авторизован)
* user - информация о пользователе

### Example response
```
{
    error: 0,
    message: '',
    data: {
        auth_token: fedcba987654321,
        user: { <user_data> }
    }
}
```
