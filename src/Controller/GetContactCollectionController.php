<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchContactsService\ContactDto;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsService;

class GetContactCollectionController implements ControllerInterface
{
    /**
     *
     *
     * @var SearchContactsService
     */
    private SearchContactsService $searchContactsService;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param SearchContactsService $searchContactsService
     */
    public function __construct(LoggerInterface $logger, SearchContactsService $searchContactsService)
    {
        $this->logger = $logger;
        $this->searchContactsService = $searchContactsService;
    }

    public function __invoke(ServerRequest $request): httpResponse
    {
        $this->logger->log("Ветка contact");
        $params = array_merge($request->getQueryParams(), $request->getAttributes());
        //$recipientsOnCategory = $this->loadData();
        $foundContact = $this->searchContactsService->search(
            (new SearchContactsCriteria())
                ->setCategory($params['category'] ?? null)
        );
        $httpCode = $this->buildHttpCode($foundContact);
        $result = $this->buildResult($foundContact);
        //$result = $this->searchForRecipientsOnCategoryInData($recipientsOnCategory,$request);
        return ServerResponseFactory::createJsonResponse($httpCode,$result);
    }

    /** Определяет http code
     * @param array $foundRecipientsOnCategory
     * @return int
     */
    protected function buildHttpCode(array $foundRecipientsOnCategory):int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundRecipientsOnCategories
     * @return array|Recipient
     */
    protected function buildResult(array $foundRecipientsOnCategories)
    {
        $result = [];
        foreach ($foundRecipientsOnCategories as $foundRecipientsOnCategory) {
            $result[] = $this->serializeContact($foundRecipientsOnCategory);
        }
        return $result;
    }


    /**
     * @param ContactDto $contactDto
     * @return array
     */
    final protected function serializeContact(ContactDto $contactDto):array
    {
        return [
            'id_recipient' => $contactDto->getIdRecipient(),
            'full_name' => $contactDto->getFullName(),
            'birthday' => $contactDto->getBirthday(),
            'profession' => $contactDto->getProfession(),
            'contract_number' => $contactDto->getContactNumber(),
            'average_transaction_amount' => $contactDto->getAverageTransactionAmount(),
            'discount' => $contactDto->getDiscount(),

        ];
    }



}