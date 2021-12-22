<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use JsonException;
use Exception;

class FindRecipient implements ControllerInterface
{
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /** Конфиг приложения
     * @var AppConfig
     */
    private AppConfig $appConfig;

    /**
     * @param LoggerInterface $logger
     * @param AppConfig $appConfig
     */
    public function __construct(LoggerInterface $logger, AppConfig $appConfig)
    {
        $this->logger = $logger;
        $this->appConfig = $appConfig;
    }

    /** Загружает данные о получателях
    * @return array
    * @throws JsonException
    */
    private function loadData():array
    {
        $loader = new JsonDataLoader();
        return $loader->loadData($this->appConfig->getPathToRecipients());
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
            'profession' => 'incorrect profession'
        ];
        $queryParams = $request->getQueryParams();
        return Assert::arrayElementsIsString($paramValidations,$queryParams);
    }
    /** Алгоритм поиска получателей
     * @param array $recipients
     * @param ServerRequest $serverRequest
     * @return array
     * @throws Exception
     */
    private function searchForRecipientsInData(array $recipients, ServerRequest $serverRequest):array
    {
        $findRecipient = [];
        $requestParams =$serverRequest->getQueryParams();
        foreach ($recipients as $recipient) {
            if (array_key_exists('id_recipient', $requestParams)) {
                $recipientMeetSearchCriteria = $requestParams['id_recipient'] === (string)$recipient['id_recipient'];
            } elseif (array_key_exists('full_name', $requestParams)) {
                $recipientMeetSearchCriteria = $requestParams['full_name'] === $recipient['full_name'];
            } elseif (array_key_exists('birthday', $requestParams)) {
                $recipientMeetSearchCriteria = $requestParams['birthday'] === $recipient['birthday'];
            } elseif (array_key_exists('profession', $requestParams)) {
                $recipientMeetSearchCriteria = $requestParams['profession'] === $recipient['profession'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if ($recipientMeetSearchCriteria) {
                $findRecipient[] = Recipient::createFromArray($recipient);
            }
        }
        $this->logger->log("Найдено получателей : " . count($findRecipient));
        return $findRecipient;
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function __invoke(ServerRequest $request): httpResponse
    {
        $this->logger->log("Ветка recipient");

        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $httpCode = 200;
            $recipients = $this->loadData();
            $result = $this->searchForRecipientsInData($recipients,$request);
        } else {
            $httpCode = 500;

            $result=[
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return ServerResponseFactory::createJsonResponse($httpCode,$result);
    }
}

