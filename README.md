## Mesh Group Test Assignment

### Assignment

Laravel (Docker, Laravel Echo, Redis, MariaDB)
Развернуть Laravel в Docker с установкой Laravel Cron и сервером очередей RabbitMQ
1. Реализовать контроллер с валидацией и загрузкой Excel файла
2. Загруженный файл через Jobs поэтапно (по 1000 строк) парсить в БД (таблица `rows`)
3. Прогресс парсинга файла хранить в Redis (уникальный ключ + количество обработанных строк)
4. Поля Excel:
- `id`
- `name`
- `date` (d.m.Y)
5. Для парсинга Excel можете использовать `maatwebsite/excel`
6. Реализовать контроллер для вывода данных (`rows`) с группировкой по `date` - двумерный массив
7. Будет плюсом если вы реализуете через Laravel Echo передачу Event-а на создание записи в `rows`
8. Написать тесты
Пример файла: https://yadi.sk/i/YuwPGwcIzv1DBQ

