# Alto CMS Restful API - resource BLOGS

## Get list of blogs

### `| GET /blogs/list?<parameters> `
Получить список блогов (с разбивкой на страницы)

_Требуется авторизация: нет_

### Parameters
* page (optional) - номер страницы (по умолчанию = 1)
* page_size (optional) - размер страницы (по умолчанию = 25)

### Return data:
* blogs - коллекция блогов
    * total - общее число блогов
    * list - список блогов (массив)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        blogs: {
            total: 1234,
            list: [
                 {<blog_data_1>},
                 {<blog_data_2>},
                 ...
            ]
        }
    }
}
```
## Get required blog

### `| GET /blogs/:id `
Возвращает данные требуемого блога

_Требуется авторизация: нет_

### Return data
* blog - информация о блоге

### Example response
```
{
    error: 0,
    message: '',
    data: {
        blog: { <blog_data> }
    }
}
```
