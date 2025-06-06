### ЗАДАЧА
* "_Тестовое задание для PHP Разработчика.docx"

### ТРЕБОВАНИЯ
* PHP v8.3x
* RMQ

### НАСТРОЙКА
* `composer install`
* настроить подключение к БД и RMQ в файле `common/config/main-local.php`
* `yii migrate`
* сооздать задачи в `/etc/supervisor/conf.d/yii_queue.conf`
```
[program:task174_queue_requests]
command=cd /path/to/project && ./yii worker/listen requests
process_name=%(program_name)s_%(process_num)02d
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/yii_queue-requests.log

[program:task174_queue_request]
command=cd /path/to/project && ./yii worker/listen request
process_name=%(program_name)s_%(process_num)02d
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/yii_queue-request.log
```

### ЗАПУСК
* `php -S'127.0.0.1:8080' -t'backend/web'`
* с помощью любого веб-сервера

### ТЕСТИРОВАНИЕ
* `Тест174.postman_collection`

### АВТОР
* Шатров Алексей Сергеевич <mail@ashatrov.ru>