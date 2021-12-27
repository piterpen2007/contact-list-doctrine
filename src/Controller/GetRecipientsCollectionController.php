<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use JsonException;
use Exception;

class GetRecipientsCollectionController implements ControllerInterface
{
    /** Путь до файла с данными о получателях
     * @var string
     */
    private string $pathToRecipients;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(string $pathToRecipients, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->pathToRecipients = $pathToRecipients;
    }

    /** Загружает данные о получателях
    * @return array
    * @throws JsonException
    */
    private function loadData():array
    {
        return (new JsonDataLoader())->loadData($this->pathToRecipients);
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
        $params = array_merge($request->getQueryParams(),$request->getAttributes());
        return Assert::arrayElementsIsString($paramValidations,$params);
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
        $searchCriteria = array_merge($serverRequest->getQueryParams(),$serverRequest->getAttributes());
        foreach ($recipients as $recipient) {
            if (array_key_exists('id_recipient', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['id_recipient'] === (string)$recipient['id_recipient'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if (array_key_exists('full_name', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['full_name'] === $recipient['full_name'];
            }
            if (array_key_exists('birthday', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['birthday'] === $recipient['birthday'];
            }
            if (array_key_exists('profession', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['profession'] === $recipient['profession'];
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
            $recipients = $this->loadData();
            $foundRecipients = $this->searchForRecipientsInData($recipients,$request);
            $httpCode = $this->buildHttpCode($foundRecipients);
            $result = $this->buildResult($foundRecipients);
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
     * @param array $foundRecipients
     * @return int
     */
    protected function buildHttpCode(array $foundRecipients):int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundRecipients
     * @return array|Recipient
     */
    protected function buildResult(array $foundRecipients)
    {
        return $foundRecipients;
    }

}

