#!/usr/bin/env_php
<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=contact_list_db";
$dbConn = new PDO($dsn, 'postgres', 'qwerty');

//$dbConnect = pg_connect('host=localhost dbname=contact_list_db user=postgres password=qwerty');
$dbConn->query('ALTER SEQUENCE recipients_id_recipient_seq RESTART WITH 1');
$dbConn->query('ALTER SEQUENCE address_id_address_seq RESTART WITH 1');
$dbConn->query('ALTER SEQUENCE contact_list_id_entry_seq RESTART WITH 1');
$dbConn->query('ALTER SEQUENCE users_id_seq RESTART WITH 1');

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
INSERT INTO recipients(full_name, birthday, profession, amount, currency,type) 
VALUES (
        '{$recipientItem['full_name']}', 
        '{$recipientItem['birthday']}',
        '{$recipientItem['profession']}',
        {$recipientItem['balance']['amount']},
        '{$recipientItem['balance']['currency']}',
        'recipient'
        )
EOF;
    echo $sql;
    $dbConn->query($sql);
}

/**
 *  Импорт родни
 */
$dbConn->query('DELETE FROM kinsfolk');

$kinsfolkData = json_decode(
    file_get_contents(__DIR__ . '/../data/kinsfolk.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($kinsfolkData as $kinsfolkItem) {
    $sql = <<<EOF
INSERT INTO recipients
(full_name, birthday, profession, amount, currency, type) 
VALUES 
(
        '{$kinsfolkItem['full_name']}', 
        '{$kinsfolkItem['birthday']}',
        '{$kinsfolkItem['profession']}',
        {$kinsfolkItem['balance']['amount']},
        '{$kinsfolkItem['balance']['currency']}',
        'kinsfolk'
);
EOF;
    $dbConn->query($sql);
    $sql = <<<EOF
INSERT INTO kinsfolk
(
    id_recipient, status, ringtone, hotkey
)  SELECT 
        max(id_recipient),
        '{$kinsfolkItem['status']}',
        '{$kinsfolkItem['ringtone']}',
        '{$kinsfolkItem['hotkey']}'
FROM recipients;
EOF;
    echo $sql;
    $dbConn->query($sql);
}

/**
 *  Импорт клиентов
 */
$dbConn->query('DELETE FROM customers');

$customersData = json_decode(
    file_get_contents(__DIR__ . '/../data/customers.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($customersData as $customerItem) {
    $sql = <<<EOF
INSERT INTO recipients
(full_name, birthday, profession, amount, currency, type) 
VALUES 
(
        '{$customerItem['full_name']}', 
        '{$customerItem['birthday']}',
        '{$customerItem['profession']}',
        {$customerItem['balance']['amount']},
        '{$customerItem['balance']['currency']}',
        'customer'
);
EOF;
    $dbConn->query($sql);
    $sql = <<<EOF
INSERT INTO customers
(
    id_recipient, contract_number, average_transaction_amount, discount, time_to_call
) 
SELECT 
        max(id_recipient),
        '{$customerItem['contract_number']}',
        {$customerItem['average_transaction_amount']},
        '{$customerItem['discount']}',
        '{$customerItem['time_to_call']}'
from recipients;
EOF;
    echo $sql;
    $dbConn->query($sql);
}


/**
 *  Импорт коллег
 */
$dbConn->query('DELETE FROM colleagues');

$colleagueData = json_decode(
    file_get_contents(__DIR__ . '/../data/colleagues.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($colleagueData as $colleagueItem) {
    $sql = <<<EOF
INSERT INTO recipients
(full_name, birthday, profession, amount, currency, type) 
VALUES 
(
        '{$colleagueItem['full_name']}', 
        '{$colleagueItem['birthday']}',
        '{$colleagueItem['profession']}',
        {$colleagueItem['balance']['amount']},
        '{$colleagueItem['balance']['currency']}',
        'colleague'
);
EOF;
    $dbConn->query($sql);
    $sql = <<<EOF
INSERT INTO colleagues
(
    id_recipient, department, position, room_number
) 
SELECT 
         max(id_recipient),
        '{$colleagueItem['department']}',
        '{$colleagueItem['position']}',
        '{$colleagueItem['room_number']}'
from recipients;
    
EOF;
    echo $sql;
    $dbConn->query($sql);
}

/**
 *  Импорт черного списка
 */
$dbConn->query('DELETE FROM contact_list');

$addressData = json_decode(
    file_get_contents(__DIR__ . '/../data/contact_list.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($addressData as $addressItem) {
    $sql = <<<EOF
INSERT INTO contact_list
(
    id_recipient, 
    blacklist
) 
VALUES ( 
        {$addressItem['id_recipient']},
        false
        )
EOF;
    echo $sql;
    $dbConn->query($sql);
}


/**
 *  Импорт адрессов
 */
$dbConn->query('DELETE FROM address');

$addressData = json_decode(
    file_get_contents(__DIR__ . '/../data/address.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

foreach ($addressData as $addressItem) {
    $sql = <<<EOF
INSERT INTO address
(
    id_recipient, 
    address, 
    status
)
VALUES (
        {$addressItem['id_recipient']}, 
        '{$addressItem['address']}',
        '{$addressItem['status']}'
        )
EOF;
    echo $sql;
    $dbConn->query($sql);
}
