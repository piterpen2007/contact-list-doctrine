<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchContactsService\ColleaguesDto;
use EfTech\ContactList\Service\SearchContactsService\CustomerDto;
use EfTech\ContactList\Service\SearchContactsService\KinsfolkDto;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;
use EfTech\ContactList\Service\SearchContactsService;

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
        if(array_key_exists('category',$params) && in_array($params['category'],['recipients','customers','kinsfolk','colleagues'])) {
            $foundContact = $this->searchContactsService->search(
                (new SearchContactsCriteria())
                    ->setCategory($params['category'] ?? null)
            );
            $httpCode = $this->buildHttpCode($foundContact);
            $result = $this->buildResult($foundContact);
        } else {
            $httpCode = 404;
            $result=[
                'status' => 'fail',
                'message' => 'dispatch category nothing'
            ];
        }

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
    protected function buildResult(array $foundRecipientsOnCategories):array
    {
        $result = [];
        foreach ($foundRecipientsOnCategories as $foundRecipientsOnCategory) {
            $result[] = $this->serializeContact($foundRecipientsOnCategory);
        }
        return $result;
    }


    /**
     * @param object $contactDto
     * @return array
     */
    final protected function serializeContact(object $contactDto):array
    {
        if ($contactDto instanceof CustomerDto) {
            return [
                'id_recipient' => $contactDto->getIdRecipient(),
                'full_name' => $contactDto->getFullName(),
                'birthday' => $contactDto->getBirthday(),
                'profession' => $contactDto->getProfession(),
                'contract_number' => $contactDto->getContactNumber(),
                'average_transaction_amount' => $contactDto->getAverageTransactionAmount(),
                'discount' => $contactDto->getDiscount(),
                'time_to_call' => $contactDto->getTimeToCall()
            ];
        }
        if ($contactDto instanceof ColleaguesDto) {
            return [
                'id_recipient' => $contactDto->getIdRecipient(),
                'full_name' => $contactDto->getFullName(),
                'birthday' => $contactDto->getBirthday(),
                'profession' => $contactDto->getProfession(),
                'department' => $contactDto->getDepartment(),
                'position' => $contactDto->getPosition(),
                'room_number'=> $contactDto->getRoomNumber()
            ];
        }
        if ($contactDto instanceof KinsfolkDto) {
            return [
                'id_recipient' => $contactDto->getIdRecipient(),
                'full_name' => $contactDto->getFullName(),
                'birthday' => $contactDto->getBirthday(),
                'profession' => $contactDto->getProfession(),
                'status' => $contactDto->getStatus(),
                'ringtone' => $contactDto->getRingtone(),
                'hotkey' => $contactDto->getHotkey()
            ];
        }
        return [
            'id_recipient' => $contactDto->getIdRecipient(),
            'full_name' => $contactDto->getFullName(),
            'birthday' => $contactDto->getBirthday(),
            'profession' => $contactDto->getProfession(),
        ];
    }



}