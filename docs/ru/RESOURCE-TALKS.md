# Alto CMS Restful API - resource TALKS

## Get list of talks

### `| GET /talks/list?<parameters> `
Получить список разговоров (с разбивкой на страницы)

_Требуется авторизация: да_

### Parameters
* page (optional) - номер страницы (по умолчанию = 1)
* page_size (optional) - размер страницы (по умолчанию = 25)

### Return data:
* talks - коллекция разговоров
    * total - общее число разговоров
    * list - список разговоров (массив)

### Example response
```
{
    error: 0,
    message: '',
    data: {
        talks: {
            total: 1234,
            list: [
                 {<talk_data_1>},
                 {<talk_data_2>},
                 ...
            ]
        }
    }
}
```
## Get required talk

### `| GET /talks/:id `
Возвращает данные требуемого разговора

_Требуется авторизация: да_

### Return data
* talk - информация о разговоре

### Example response
```
{
    error: 0,
    message: '',
    data: {
        talk: { <talk_data> }
    }
}
```
