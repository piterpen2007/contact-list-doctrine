<?php
namespace EfTech\ContactList\Controller;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use function EfTech\ContactList\Infrastructure\loadData;
use function EfTech\ContactList\Infrastructure\paramTypeValidation;


require_once __DIR__ . '/../Infrastructure/app.function.php';

/**
 * Функция поиска знакомых по id или full_name
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger LoggerInterface - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по знакомым
 * @throws \Exception
 */
return static function (ServerRequest $request, LoggerInterface $logger, AppConfig $appConfig):httpResponse
{
    $recipients = loadData($appConfig->getPathToRecipients());
    $logger->log('dispatch "recipient" url');

    $paramValidations = [
        'id_recipient' => 'incorrect id_recipient',
        'full_name' =>'incorrect full_name',
        'birthday' => 'incorrect birthday',
        'profession' => 'incorrect profession'
    ];
    $requestParams = $request->getQueryParams();
    if(null === ($result = paramTypeValidation($paramValidations, $requestParams))) {
        $foundRecipients = [];
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
                $foundRecipients[] = Recipient::createFromArray($recipient);
            }
        }
        $logger->log('found recipients not category: ' . count($foundRecipients));
        $result = [
            'httpCode' => 200,
            'result' => $foundRecipients
        ];
    }
    return ServerResponseFactory::createJsonResponse($result['httpCode'],$result['result']);
};
