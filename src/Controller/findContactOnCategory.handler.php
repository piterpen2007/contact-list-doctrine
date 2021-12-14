<?php
namespace EfTech\ContactList\Controller;


use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use function EfTech\ContactList\Infrastructure\loadData;
use function EfTech\ContactList\Infrastructure\paramTypeValidation;

require_once __DIR__ . '/../Entity/Recipient.php';
require_once __DIR__ . '/../Entity/Customer.php';
require_once __DIR__ . '/../Entity/Colleague.php';
require_once __DIR__ . '/../Entity/Kinsfolk.php';
require_once __DIR__ . '/../Infrastructure/AppConfig.php';
require_once __DIR__ . '/../Infrastructure/app.function.php';
require_once __DIR__ . '/../Infrastructure/Logger/LoggerInterface.php';

/**
 * Функция поиска контакттов по категории
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по категориям
 */
return static function (array $request, LoggerInterface $logger, AppConfig $appConfig):array
{
    $customers = loadData($appConfig->getPathToCustomers());
    $recipients = loadData($appConfig->getPathToRecipients());
    $kinsfolk = loadData($appConfig->getPathToKinsfolk());
    $colleagues = loadData($appConfig->getPathToColleagues());

    $logger->log('dispatch "category" url');

    if (!array_key_exists('category', $request)) {
        return [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => 'empty category'
            ]
        ];
    }
    $foundRecipientsOnCategory = [];
    if ($request['category'] === 'customers') {
        foreach ($customers as $customer) {
            $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
        }
        $logger->log('dispatch category "customers"');
        $logger->log('found customers: '. count($foundRecipientsOnCategory));
    } elseif ($request['category'] === 'recipients') {
        foreach ($recipients as $recipient) {
            $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
        }
        $logger->log('dispatch category "recipients"');
        $logger->log('found customers: '. count($foundRecipientsOnCategory));
    } elseif ($request['category'] === 'kinsfolk') {
        foreach ($kinsfolk as $kinsfolkValue) {
            $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
        }
        $logger->log('dispatch category "kinsfolk"');
        $logger->log('found kinsfolk: '. count($foundRecipientsOnCategory));
    } elseif ($request['category'] === 'colleagues') {
        foreach ($colleagues as $colleague) {
            $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
        }
        $logger->log('dispatch category "colleagues"');
        $logger->log('found colleagues: '. count($foundRecipientsOnCategory));
    } else {
        return [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => 'dispatch category nothing'
            ]
        ];
    }
    return [
        'httpCode' => 200,
        'result' => $foundRecipientsOnCategory
    ];
};