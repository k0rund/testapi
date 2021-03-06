# HTTP API

1. Скачиваем проект.
2. В файл /etc/hosts (операционная система linux) добавляем строчку 127.0.0.1   testapi.local
3. В коневой директории, rде лежит файл docker-compose.yml в терминале выполняем команду docker-compose up --build
4. Из корневой директории преходим в директории www и вылняем команду php composer.phar install для установки пакетов
5. Выставляем права на директроию mysql и files (если потребуется)
6. Для запуска тестов необходимо зайти в контейнер с php и выполнить команду vendor/bin/codecept run

## API

1. Cоздание пользователя
Пример запроса
http://testapi.local/api/createuser

#### Метод
POST
#### Параметры
login и pass
#### Кодировка                   
multipart/form-data
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}

2. Авторизация пользователя
Пример запроса
http://testapi.local/api/login

#### Метод
POST
#### Параметры
login и pass
#### Кодировка                   
multipart/form-data
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}

3. Разлогинивание пользователя
Пример запроса
http://testapi.local/api/logout

#### Метод
POST
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}

4. Создать файл из данных в запросе
Пример запроса
http://testapi.local/api/files_put?filename=kamaz-dakar.jpg

#### Метод
PUT
#### Параметры
filename передается в адресной строке Файл передается в теле запроса.
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}

5. Обновить содержимое файла из данных в запросе
Пример запроса
http://testapi.local/api/files_put?filename=kamaz-dakar-2.jpg&id=13

#### Метод
PUT
#### Параметры
filename и id передаются в адресной строке. Файл передается в теле запроса.
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}


6. Получить содержимое файла
Пример запроса
http://testapi.local/api/get_file?id=13

#### Метод
GET
#### Параметры
id передается в адресной строке.
#### Возвращаемое значение       
Статус 404 - если файл не найден.
Статус 200 - если файл найден. Файл в бинарном виде.


7. Получить список файлов
Пример запроса
http://testapi.local/api/files_list

#### Метод
GET
#### Возвращаемое значение       
Тип JSON. Успех {"status": 1} Неудача {"status": 0, "message": "Текст ошибки"}

