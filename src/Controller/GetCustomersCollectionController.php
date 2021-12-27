<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use Exception;
use JsonException;

class GetCustomersCollectionController implements ControllerInterface
{
    /** Путь до файла с данными о клиентах
     * @var string
     */
    private string $pathToCustomers;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;


    /**
     * @param LoggerInterface $logger
     */
    public function __construct(string $pathToCustomers, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->pathToCustomers = $pathToCustomers;

    }

    /** Загружает данные о клиентах
     * @return array
     * @throws JsonException
     */
    private function loadData():array
    {
        return (new JsonDataLoader())->loadData($this->pathToCustomers);
    }
    /**  Валдирует параматры запроса
     * @param ServerRequest $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequest $request):?string
    {
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
        $params = array_merge($request->getQueryParams(),$request->getAttributes());
        return Assert::arrayElementsIsString($paramValidations,$params);
    }
    /** Алгоритм поиска клиетов
     * @param array $customers
     * @param ServerRequest $serverRequest
     * @return array
     * @throws Exception
     */
    private function searchForCustomersInData(array $customers, ServerRequest $serverRequest):array
    {
        $foundCustomers =[];
        $searchCriteria = array_merge($serverRequest->getQueryParams(),$serverRequest->getAttributes());
        foreach ($customers as $customer) {
            if (array_key_exists('id_recipient', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['id_recipient'] === (string)$customer['id_recipient'];
            } elseif (array_key_exists('full_name', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['full_name'] === $customer['full_name'];
            } elseif (array_key_exists('birthday', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['birthday'] === $customer['birthday'];
            } elseif (array_key_exists('profession', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['profession'] === $customer['profession'];
            } elseif (array_key_exists('contract_number', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['contract_number'] === $customer['contract_number'];
            } elseif (array_key_exists('average_transaction_amount', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['average_transaction_amount'] === (string)$customer['average_transaction_amount'];
            } elseif (array_key_exists('discount', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['discount'] === $customer['discount'];
            } elseif (array_key_exists('time_to_call', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['time_to_call'] === $customer['time_to_call'];
            } else {
                $customerMeetSearchCriteria = true;
            }

            if ($customerMeetSearchCriteria) {
                $foundCustomers[] = Customer::createFromArray($customer);
            }
        }
        $this->logger->log("Найдено получателей : " . count($foundCustomers));
        return $foundCustomers;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function __invoke(ServerRequest $request): httpResponse
    {
        $this->logger->log("Ветка customer");

        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $customers = $this->loadData();
            $foundCustomers = $this->searchForCustomersInData($customers,$request);
            $httpCode = $this->buildHttpCode($foundCustomers);
            $result = $this->buildResult($foundCustomers);
        } else {
            $httpCode = 500;

            $result=[
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return ServerResponseFactory::createJsonResponse($httpCode,$result);
    }
    /** Определяет http code
     * @param array $foundCustomers
     * @return int
     */
    protected function buildHttpCode(array $foundCustomers):int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundCustomers
     * @return array|Customer
     */
    protected function buildResult(array $foundCustomers)
    {
        return $foundCustomers;
    }
}