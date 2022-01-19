<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

require_once __DIR__ . '/../../src/Infrastructure/Autoloader.php';

use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Autoloader;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchRecipientsService;
use EfTech\ContactListTest\TestUtils;



spl_autoload_register(
    new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../../test/'
    ])
);

/**
 * Тестирование контроллера FindAuthors
 */
class FindRecipientTest
{
    /** Тестирование поиска авторов по фамилии
     * @return void
     * @throws \JsonException
     */
    public static function testSearchAuthorsBySurname():void
    {
        echo "-------------------Тестирование поиска автора по фамилии-----------------------\n";
        $httpRequest = new ServerRequest (
            'GET',
            '1.1',
            '/recipient?full_name=Осипов Геннадий Иванович',
            Uri::createFromString('http://localhost:8082/recipient?full_name=Осипов Геннадий Иванович'),
            ['Content-Type'=> 'application/json'],
            null
        );
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $diContainer = new Container(
            [
                LoggerInterface::class => new Logger(new NullAdapter()),
                'pathToRecipients' => $appConfig->getPathToRecipients()
            ],
            [
                GetRecipientsCollectionController::class => [
                    'args' => [
                        'logger' => LoggerInterface::class,
                        'searchRecipientsService' => SearchRecipientsService::class
                    ]
                ],
                SearchRecipientsService::class => [
                    'args' => [
                        'logger' => LoggerInterface::class,
                        'recipientRepository' => RecipientRepositoryInterface::class
                    ]

                ],
                RecipientRepositoryInterface::class => [
                    'class' => RecipientJsonFileRepository::class,
                    'args' => [
                        'pathToRecipients' => 'pathToRecipients',
                        'dataLoader' => DataLoaderInterface::class
                    ]
                ],
                DataLoaderInterface::class => [
                    'class' => JsonDataLoader::class
                ],
            ]
        );

        $findRecipients = $diContainer->get(GetRecipientsCollectionController::class);
        $httpResponse = $findRecipients($httpRequest);
        //Assert
        if ($httpResponse->getStatusCode() === 200) {
            echo "    OK --- код ответа\n";
        } else {
            echo "    FAIL - код ответа. Ожидалось: 200. Актуальное значение: {$httpResponse->getStatusCode()}\n";
        }
        $expected = [
            [
                'id_recipient'=> 1,
                'full_name'=> 'Осипов Геннадий Иванович',
                'birthday'=>'15.06.1985',
                'profession'=> 'Системный администратор',
            ]
        ];

        $actualResult =  json_decode($httpResponse->getBody(), true, 512 , JSON_THROW_ON_ERROR);

        $unnecessaryElements = TestUtils::arrayDiffAssocRecursive($actualResult, $expected);
        $missingElements =  TestUtils::arrayDiffAssocRecursive($expected, $actualResult);

        $errMsg = '';

        if (count($unnecessaryElements) > 0) {
            $errMsg .= sprintf("         Есть лишние элементы %s\n", json_encode($unnecessaryElements,JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        }
        if (count($missingElements) > 0) {
            $errMsg .= sprintf("         Есть лишние недостающие элементы %s\n", json_encode($missingElements,JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        }

        if ('' === $errMsg) {
            echo "    ОК- данные ответа валидны\n";
        } else {
            echo "    FAIL - данные ответа валидны\n" . $errMsg;
        }

    }
}
FindRecipientTest::testSearchAuthorsBySurname();