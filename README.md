# DeftCMS mail library 

Библиотека загружает модель `Core/BaseEmailTemplate` для загрузки шаблонов сообщений.

### Настройки
Настройки отправки сообщений.

Для более подробной информацией перейдите по ссылке [CI_Email](https://codeigniter.com/userguide3/libraries/email.html#email-preferences)

```php
$config['email'] = [
    'protocol'          => 'mail', 
    'mailpath'          => '/usr/sbin/sendmail',
    'smtp_crypto'       => '',
    'smtp_host'         => 'localhost',
    'smtp_pass'         => '',
    'smtp_port'         => 25,
    'smtp_timeout'      => 3,
    'bcc_batch_mode'    => 1,
    // Сервисный email с которого будут отправлятся письма
    'service_email'         => '',
    //  Подставляется вместо email "Название бренда"
    'service_email_title'   => ''
];
```

### Структура базы данных

```
'fk_c_locale_code' => [
    'type'           => 'char',
    'constraint'     => 5,
    'null'           => false,
],
's_name' => [
    'type'           => 'varchar',
    'constraint'     => 20,
],
's_subject' => [
    'type'           => 'varchar',
    'constraint'     => 120,
],
's_message' => [
    'type'           => 'text',
    'constraint'     => 0,
],
'i_priority' => [
    'type'           => 'tinyint',
    'constraint'     => 1,
    'default'        => 3
]
```

### Использование

```php

$success = MailFactory::get()->send(
    $subject = 'subject', // Субект письма
    $message = 'message', // Сообшение письма
    $to = 'example@example.ru',    // Получатель Email
    // Email отправителя (если не указан используется глобальная настройка service_email)
    $from = 'example@example.ru',
    // Имя отправителя (если не указан используется глобальная настройка service_email_title)
    $name = null
);

$success = MailFactory::get()->template(
    $name = 'authorize',  // Название шаблона
    $to = 'example@example.ru',    // Получатель Email
    // Email отправителя (если не указан используется глобальная настройка service_email)
    $from = 'example@example.ru',
    // Дополнительные переменный для письма __%extra%__
    $extra = [ 'extra' => '1'  ]
);
```