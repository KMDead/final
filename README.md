Инструкция по использованию приложения

Предварительно необходимо создать БД (sql скрипт в папке resources)

чтобы начать работу необходимо зарегистрировать нового пользователя:
post-запрос
"../user/registration"
параметры
name = 
organisation = 
email = 
phone = 
password =



*далее необходимо пройти аутентификацию (необходимо при каждом входе в систему):
"../api/user/authentication"
параметры
email = 
password =



изменение данных пользователя:
"../api/user/update"
name = 
organisation = 
email = 
phone = 
password =



создать склад
"../api/warehouse/add"
post-запрос
параметры
address = 
capacity =



обновить склад
"../api/warehouse/update"
post-запрос
параметры
address = 
capacity =



удалить склад
"../api/warehouse/delete"
post-запрос
параметры
address = 
[
	capacity = (можно не указывать)
]



получить склад по адресу
"../api/weaehouse/address/{address}"
get-запрос



получить склад по id
"../api/weaehouse/id/{id}"
get-запрос



создать продукт
"../api/items/create"
post-запрос
параметры
name = 
price =
size =
type =
(*продукт есть в БД, но пока не учитывается ни на одном из складов)



изменить характеристики продукта
"../api/items/update"
post-запрос
параметры
name = 
price =
size =
type =



удалить продукт
"../api/items/remove"
post-запрос
параметры
name = 
[
	price =
	size =
	type = 
	(можно не указывать)
]



добавить некоторое количество продукта на склад
"../api/items/add"
post-запрос
параметры
source_name = имя отправителя
address = адрес склада-получателя
items = (через запятую id продуктов)
quantity = (через запятую количество продуктов)
(*везде необходимо указывать, откуда этот продукт приходит)


вычесть некоторое количество продукта со склада
"../api/items/sub"
post-запрос
параметры
destiny_name = имя получателя
address = адрес склада-источника
items = (через запятую id продуктов)
quantity = (через запятую количество продуктов)
(*везде необходимо указывать, куда этот продукт уходит)



переместить продукт между складами
"../api/items/mov"
post-запрос
параметры
address_destiny = адрес склада-получателя
address_source = адрес склада-источника
items = (через запятую id продуктов)
quantity = (через запятую количество продуктов)




получить список продуктов
"../api/items"
get-запрос



получить продукты на складе (без параметра date - на данный момент, с параметром - до момента date)
"../api/items/warehouse/{id_warehouse}?date="YYYY-MM-DD HH:MM:SS"
get-запрос



получить данный продукт на складах (без параметра date - на данный момент, с параметром - до момента date)
"../api/items/name/{name}?date="YYYY-MM-DD HH:MM:SS" 
"../api/items/id/{id}?date="YYYY-MM-DD HH:MM:SS" 
get-запрос



получить все перемещения
"../api/transfers"
get-запрос



получить перемещения продуктов по данному складу
"../api/transfers/warehouse/{address}"
get-запрос



получить перемещения конкретного продукта по складам
"../api/transfers/item/{name}"
get-запрос


