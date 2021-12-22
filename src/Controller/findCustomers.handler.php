<?php
namespace EfTech\ContactList\Controller;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use function EfTech\ContactList\Infrastructure\loadData;
use function EfTech\ContactList\Infrastructure\paramTypeValidation;

require_once __DIR__ . '/../Infrastructure/app.function.php';

/**
 * Функция поиска клиента по id или full_name
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по авторам
 */

return static function (ServerRequest $request, LoggerInterface $logger,AppConfig $appConfig):httpResponse
{
    $customers = loadData($appConfig->getPathToCustomers());
    $logger->log('dispatch "customers" url');
    $paramValidations = [
        'id_recipient' => 'incorrect id_recipient',
        'full_name' =>'incorrect full_name',
        'birthday' => 'incorrect birthday',
        'profession' => 'incorrect profession',
        'contract_number' => ' incorrect contract_number',
        'average_transaction_amount' => 'incorrect average_transaction_amount',
        'discount' => 'incorrect discount',
        'time_to_call' => 'incorrect time_to_call'
    ];
    $requestParams = $request->getQueryParams();

    if(null === ($result = paramTypeValidation($paramValidations, $requestParams))) {
        $foundCustomers =[];
        foreach ($customers as $customer) {
            if (array_key_exists('id_recipient', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['id_recipient'] === (string)$customer['id_recipient'];
            } elseif (array_key_exists('full_name', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['full_name'] === $customer['full_name'];
            } elseif (array_key_exists('birthday', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['birthday'] === $customer['birthday'];
            } elseif (array_key_exists('profession', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['profession'] === $customer['profession'];
            } elseif (array_key_exists('contract_number', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['contract_number'] === $customer['contract_number'];
            } elseif (array_key_exists('average_transaction_amount', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['average_transaction_amount'] === (string)$customer['average_transaction_amount'];
            } elseif (array_key_exists('discount', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['discount'] === $customer['discount'];
            } elseif (array_key_exists('time_to_call', $requestParams)) {
                $customerMeetSearchCriteria = $requestParams['time_to_call'] === $customer['time_to_call'];
            } else {
                $customerMeetSearchCriteria = true;
            }
            if ($customerMeetSearchCriteria) {
                $foundCustomers[] = Customer::createFromArray($customer);
            }
        }
        $logger->log('found customers not category: ' . count($foundCustomers));
        $result = [
            'httpCode' => 200,
            'result' => $foundCustomers
        ];
    }
    return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
};