<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchCustomersService\CustomerDto;
use EfTech\ContactList\Service\SearchCustomersService\SearchCustomersCriteria;
use EfTech\ContactList\Service\SearchCustomersService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetCustomersCollectionController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /**
     *
     *
     * @var SearchCustomersService
     */
    private SearchCustomersService $searchCustomersService;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param SearchCustomersService $searchCustomersService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SearchCustomersService $searchCustomersService,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory
    ) {
        $this->logger = $logger;
        $this->searchCustomersService = $searchCustomersService;
        $this->serverResponseFactory = $serverResponseFactory;
    }

    /**  Валдирует параматры запроса
     * @param ServerRequestInterface $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequestInterface $request): ?string
    {
        $paramValidations = [
            'id_recipient' => 'incorrect id_recipient',
            'full_name' => 'incorrect full_name',
            'birthday' => 'incorrect birthday',
            'profession' => 'incorrect profession',
            'contract_number' => ' incorrect contract_number',
            'average_transaction_amount' => 'incorrect average_transaction_amount',
            'discount' => 'incorrect discount',
            'time_to_call' => 'incorrect time_to_call'
        ];
        $params = array_merge($request->getQueryParams(), $request->getAttributes());
        return Assert::arrayElementsIsString($paramValidations, $params);
    }

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("Ветка customer");

        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundCustomers = $this->searchCustomersService->search(
                (new SearchCustomersCriteria())
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setFullName($params['full_name'] ?? null)
                    ->setBirthday($params['birthday'] ?? null)
                    ->setProfession($params['profession'] ?? null)
                ->setContactNumber($params['contract_number'] ?? null)
                ->setAverageTransactionAmount($params['average_transaction_amount'] ?? null)
                ->setDiscount($params['discount'] ?? null)
                ->setTimeToCall($params['time_to_call'] ?? null)
            );
            $httpCode = $this->buildHttpCode($foundCustomers);
            $result = $this->buildResult($foundCustomers);
        } else {
            $httpCode = 500;

            $result = [
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return $this->serverResponseFactory->createJsonResponse($httpCode, $result);
    }
    /** Определяет http code
     * @param array $foundCustomers
     * @return int
     */
    protected function buildHttpCode(array $foundCustomers): int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundCustomers
     * @return array|Customer
     */
    protected function buildResult(array $foundCustomers)
    {
        $result = [];
        foreach ($foundCustomers as $foundCustomer) {
            $result[] = $this->serializeCustomer($foundCustomer);
        }
        return $result;
    }


    /**
     * @param CustomerDto $customerDto
     * @return array|Customer
     */
    final protected function serializeCustomer(CustomerDto $customerDto): array
    {
        return [
            'id_recipient' => $customerDto->getIdRecipient(),
            'full_name' => $customerDto->getFullName(),
            'birthday' => $customerDto->getBirthday(),
            'profession' => $customerDto->getProfession(),
            'contract_number' => $customerDto->getContactNumber(),
            'average_transaction_amount' => $customerDto->getAverageTransactionAmount(),
            'discount' => $customerDto->getDiscount(),
            'time_to_call' => $customerDto->getTimeToCall(),
        ];
    }
}
