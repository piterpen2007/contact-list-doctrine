#!/usr/bin/env_php
<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=contact_list_db";
$dbConn = new PDO($dsn, 'postgres', 'qwerty');

//$dbConnect = pg_connect('host=localhost dbname=contact_list_db user=postgres password=qwerty');


/**
 * Импорт данных пользователя
 */
$dbConn->query('DELETE FROM users');

$userData = json_decode(
    file_get_contents(__DIR__ . '/../data/users.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($userData as $userItem) {
    $sql =
        "INSERT INTO users(id, login, password) 
values ({$userItem['id']}, '{$userItem['login']}', '{$userItem['password']}')";
    echo $sql;
    $dbConn->query($sql);
}

$userFromDb = $dbConn->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);

/**
 *  Импорт получателей
 */
$dbConn->query('DELETE FROM recipients');

$recipientData = json_decode(
    file_get_contents(__DIR__ . '/../data/recipient.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($recipientData as $recipientItem) {
    $sql = <<<EOF
INSERT INTO recipients(id_recipient, full_name, birthday, profession, amount, currency) 
VALUES (
        {$recipientItem['id_recipient']},
        '{$recipientItem['full_name']}', 
        '{$recipientItem['birthday']}',
        '{$recipientItem['profession']}',
        {$recipientItem['balance']['amount']},
        '{$recipientItem['balance']['currency']}'
        )
EOF;
    echo $sql;
    $dbConn->query($sql);
}

/**
 *  Импорт получателей
 */
$dbConn->query('DELETE FROM recipients');

$recipientData = json_decode(
    file_get_contents(__DIR__ . '/../data/recipient.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($recipientData as $recipientItem) {
    $sql = <<<EOF
INSERT INTO recipients(id_recipient, full_name, birthday, profession, amount, currency) 
VALUES (
        {$recipientItem['id_recipient']},
        '{$recipientItem['full_name']}', 
        '{$recipientItem['birthday']}',
        '{$recipientItem['profession']}',
        {$recipientItem['balance']['amount']},
        '{$recipientItem['balance']['currency']}'
        )
EOF;
    echo $sql;
    $dbConn->query($sql);
}
