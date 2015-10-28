# Alto CMS Restful API - resource COMMENTS

## Get list of comments

### `| GET /comments/list?<parameters> `
Получить список комментариев (с разбивкой на страницы)

_Требуется авторизация: нет_

### Parameters
* page (optional) - номер страницы (по умолчанию = 1)
* page_size (optional) - размер страницы (по умолчанию = 25)

### Return data:
* comments - коллекция комментариев
    * total - общее число комментариев
    * list - список комментариев (массив)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        comments: {
            total: 1234,
            list: [
                 {<comment_data_1>},
                 {<comment_data_2>},
                 ...
            ]
        }
    }
}
```
## Get required comment

### `| GET /comments/:id `
Возвращает данные требуемого комментария

_Требуется авторизация: нет_

### Return data
* comment - информация о комментарии

### Example response
```
{
    error: 0,
    message: '',
    data: {
        comment: { <comment_data> }
    }
}
```
