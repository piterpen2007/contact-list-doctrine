<?php
namespace EfTech\ContactList\Controller;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use function EfTech\ContactList\Infrastructure\loadData;
use function EfTech\ContactList\Infrastructure\paramTypeValidation;

require_once __DIR__ . '/../Entity/Recipient.php';
require_once __DIR__ . '/../Infrastructure/AppConfig.php';
require_once __DIR__ . '/../Infrastructure/app.function.php';
require_once __DIR__ . '/../Infrastructure/Logger/LoggerInterface.php';
/**
 * Функция поиска знакомых по id или full_name
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по знакомым
 */
return static function (array $request, LoggerInterface $logger, AppConfig $appConfig):array
{
    $recipients = loadData($appConfig->getPathToRecipients());
    $logger->log('dispatch "recipient" url');

    $paramValidations = [
        'id_recipient' => 'incorrect id_recipient',
        'full_name' =>'incorrect full_name',
        'birthday' => 'incorrect birthday',
        'profession' => 'incorrect profession'
    ];

    if(null === ($result = paramTypeValidation($paramValidations, $request))) {
        $foundRecipients = [];
        foreach ($recipients as $recipient) {
            if (array_key_exists('id_recipient', $request)) {
                $recipientMeetSearchCriteria = $request['id_recipient'] === (string)$recipient['id_recipient'];
            } elseif (array_key_exists('full_name', $request)) {
                $recipientMeetSearchCriteria = $request['full_name'] === $recipient['full_name'];
            } elseif (array_key_exists('birthday', $request)) {
                $recipientMeetSearchCriteria = $request['birthday'] === $recipient['birthday'];
            } elseif (array_key_exists('profession', $request)) {
                $recipientMeetSearchCriteria = $request['profession'] === $recipient['profession'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if ($recipientMeetSearchCriteria) {
                $foundRecipients[] = Recipient::createFromArray($recipient);
            }
        }
        $logger->log('found recipients not category: ' . count($foundRecipients));
        return [
            'httpCode' => 200,
            'result' => $foundRecipients
        ];
    }
    return $result;
};
