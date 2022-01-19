<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\ContactList;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchContactListService;
use EfTech\ContactList\Service\SearchContactListService\ContactListDto;
use EfTech\ContactList\Service\SearchContactListService\SearchContactListCriteria;

class GetContactListCollectionController implements ControllerInterface
{
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     *
     *
     * @var SearchContactListService
     */
    private SearchContactListService $contactListService;

    /**
     * @param LoggerInterface $logger
     * @param SearchContactListService $contactListService
     */
    public function __construct(LoggerInterface $logger, SearchContactListService $contactListService)
    {
        $this->logger = $logger;
        $this->contactListService = $contactListService;
    }

    /**  Валдирует параматры запроса
     * @param ServerRequest $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequest $request):?string
    {
        $paramTypeValidation = [
            'id_recipient' => "incorrect id_recipient",
            'id_entry' => 'incorrect id_entry',
            'blacklist' => 'incorrect blacklist'
        ];
        $queryParams = array_merge($request->getQueryParams(),$request->getAttributes());
        return Assert::arrayElementsIsString($paramTypeValidation,$queryParams);
    }

    /** Реализация поиска контактов по списку
     * @param ServerRequest $request - серверный объект запроса
     * @return httpResponse - объект http ответа

     */
    public function __invoke(ServerRequest $request): HttpResponse
    {
        $this->logger->info("Ветка contact-list");
        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundContactLists = $this->contactListService
                ->search((new SearchContactListCriteria())
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setIdEntry($params['id_entry'] ?? null)
                    ->setBlacklist($params['blacklist'] ?? null)
                );

            $result = $this->buildResult($foundContactLists);
            $httpCode = $this->buildHttpCode($foundContactLists);


        } else {
            $httpCode = 500;
            $result=[
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return ServerResponseFactory::createJsonResponse($httpCode,$result);

    }

    /** Подготавливает данные для ответа
     * @param array $foundContactLists
     * @return array|ContactList
     */
    protected function buildResult(array $foundContactLists)
    {
        $result = [];
        foreach ($foundContactLists as $foundContactList) {
            $result[] = $this->serializeContactList($foundContactList);
        }
        return $result;

    }

    /** Подготавливает http code
     * @param array $foundContactList
     * @return int
     */
    protected function buildHttpCode(array $foundContactList):int
    {
        return 200;
    }

    /**
     * @param ContactListDto $contactListDto
     * @return array
     */
    final protected function serializeContactList(ContactListDto $contactListDto):array
    {
        return [
            'id_recipient' => $contactListDto->getIdRecipient(),
            'id_entry' => $contactListDto->getIdEntry(),
            'blacklist' => $contactListDto->getBlacklist()
        ];
    }
}