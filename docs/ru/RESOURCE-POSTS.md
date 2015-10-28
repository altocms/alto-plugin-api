# Alto CMS Restful API - resource POSTS

## Get list of posts

### `| GET /posts/list?<parameters> `
Получить список статей (с разбивкой на страницы)

_Требуется авторизация: нет_

### Parameters
* page (optional) - номер страницы (по умолчанию = 1)
* page_size (optional) - размер страницы (по умолчанию = 25)

### Return data:
* posts - коллекция статей
    * total - общее число статей
    * list - список статей (массив)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        posts: {
            total: 1234,
            list: [
                 {<post_data_1>},
                 {<post_data_2>},
                 ...
            ]
        }
    }
}
```
## Get required post

### `| GET /posts/:id `
Возвращает данные требуемой статьи

_Требуется авторизация: нет_

### Return data
* post - информация о статье

### Example response
```
{
    error: 0,
    message: '',
    data: {
        post: { <post_data> }
    }
}
```
### `| GET /posts/:id/comments `
Возвращает комментарии требуемой статьи

_Требуется авторизация: нет_

### Return data
* post - информация о статье
* comments - коллекция комментариев
  * total
  * list

### Example response
```
{
    error: 0,
    message: '',
    data: {
        post: { <post_data> },
        comments: {
            total: 123,
            list: [
                { <comment_data_1> },
                { <comment_data_2> },
                ...
            ]
        }
    }
}
```
