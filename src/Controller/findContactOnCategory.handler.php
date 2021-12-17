<?php
namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use function EfTech\ContactList\Infrastructure\loadData;

require_once __DIR__ . '/../Infrastructure/app.function.php';


/**
 * Функция поиска контакттов по категории
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по категориям
 */
return static function (ServerRequest $request, LoggerInterface $logger, AppConfig $appConfig):httpResponse
{
    $customers = loadData($appConfig->getPathToCustomers());
    $recipients = loadData($appConfig->getPathToRecipients());
    $kinsfolk = loadData($appConfig->getPathToKinsfolk());
    $colleagues = loadData($appConfig->getPathToColleagues());

    $logger->log('dispatch "category" url');

    $requestParams = $request->getQueryParams();

    if (!array_key_exists('category', $requestParams)) {
        $result = [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => 'empty category'
            ]
        ];
        return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
    }
    $foundRecipientsOnCategory = [];
    if ($requestParams['category'] === 'customers') {
        foreach ($customers as $customer) {
            $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
        }
        $logger->log('dispatch category "customers"');
        $logger->log('found customers: '. count($foundRecipientsOnCategory));
    } elseif ($requestParams['category'] === 'recipients') {
        foreach ($recipients as $recipient) {
            $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
        }
        $logger->log('dispatch category "recipients"');
        $logger->log('found customers: '. count($foundRecipientsOnCategory));
    } elseif ($requestParams['category'] === 'kinsfolk') {
        foreach ($kinsfolk as $kinsfolkValue) {
            $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
        }
        $logger->log('dispatch category "kinsfolk"');
        $logger->log('found kinsfolk: '. count($foundRecipientsOnCategory));
    } elseif ($requestParams['category'] === 'colleagues') {
        foreach ($colleagues as $colleague) {
            $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
        }
        $logger->log('dispatch category "colleagues"');
        $logger->log('found colleagues: '. count($foundRecipientsOnCategory));
    } else {
        $result =  [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => 'dispatch category nothing'
            ]
        ];
        return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
    }
    $result = [
        'httpCode' => 200,
        'result' => $foundRecipientsOnCategory
    ];
    return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
};