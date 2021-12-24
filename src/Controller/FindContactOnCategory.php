<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use JsonException;

class FindContactOnCategory implements ControllerInterface
{
    private string $pathToCustomers;
    private string $pathToRecipients;
    private string $pathToKinsfolk;
    private string $pathToColleagues;

    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param string $pathToCustomers
     * @param string $pathToRecipients
     * @param string $pathToKinsfolk
     * @param string $pathToColleagues
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $pathToCustomers,
        string $pathToRecipients,
        string $pathToKinsfolk,
        string $pathToColleagues,
        LoggerInterface $logger
    ) {
        $this->pathToCustomers = $pathToCustomers;
        $this->pathToRecipients = $pathToRecipients;
        $this->pathToKinsfolk = $pathToKinsfolk;
        $this->pathToColleagues = $pathToColleagues;
        $this->logger = $logger;
    }


    /** Загружает данные о получателях по категориям
     * @return array
     * @throws JsonException
     */
    private function loadData():array
    {
        $loader = new JsonDataLoader();
        $customers = $loader->loadData($this->pathToCustomers);
        $recipients = $loader->loadData($this->pathToRecipients);
        $kinsfolk = $loader->loadData($this->pathToKinsfolk);
        $colleague = $loader->loadData($this->pathToColleagues);

        return [
            'customers' => $customers,
            'recipients' => $recipients,
            'kinsfolk' => $kinsfolk,
            'colleagues' => $colleague
        ];
    }
    private function searchForRecipientsOnCategoryInData(array $recipientsOnCategory,ServerRequest $request):array
    {
        $foundRecipientsOnCategory = [];
        $requestParams = $request->getQueryParams();

        if (!array_key_exists('category', $requestParams)) {
            return [
                'httpCode' => 500,
                'result' => [
                    'status' => 'fail',
                    'message' => 'empty category'
                ]
            ];
        }
        if ($requestParams['category'] === 'customers') {
            foreach ($recipientsOnCategory['customers'] as $customer) {
                $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
            }
            $this->logger->log('dispatch category "customers"');
            $this->logger->log('found customers: '. count($foundRecipientsOnCategory));
        } elseif ($requestParams['category'] === 'recipients') {
            foreach ($recipientsOnCategory['recipients'] as $recipient) {
                $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
            }
            $this->logger->log('dispatch category "recipients"');
            $this->logger->log('found customers: '. count($foundRecipientsOnCategory));
        } elseif ($requestParams['category'] === 'kinsfolk') {
            foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
            }
            $this->logger->log('dispatch category "kinsfolk"');
            $this->logger->log('found kinsfolk: '. count($foundRecipientsOnCategory));
        } elseif ($requestParams['category'] === 'colleagues') {
            foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
            }
            $this->logger->log('dispatch category "colleagues"');
            $this->logger->log('found colleagues: '. count($foundRecipientsOnCategory));
        } else {
            return  [
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

    }

    /**
     * @throws JsonException
     */
    public function __invoke(ServerRequest $request): httpResponse
    {
        $recipientsOnCategory = $this->loadData();
        $result = $this->searchForRecipientsOnCategoryInData($recipientsOnCategory,$request);
        return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
    }

}