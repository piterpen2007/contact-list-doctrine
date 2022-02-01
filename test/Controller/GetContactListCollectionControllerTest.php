<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetContactListCollectionController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Repository\ContactListJsonRepository;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchContactListService;
use EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;

class GetContactListCollectionControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchContactList(): void
    {
        //Arrange
        $httpRequest = new ServerRequest(
            'GET',
            '1.1',
            '/contactList?id_recipient=1',
            Uri::createFromString('http://localhost:8082/contactList?id_recipient=1'),
            ['Content-Type' => 'application/json'],
            null
        );
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new Logger(new NullAdapter());

        $controller = new GetContactListCollectionController(
            $logger,
            new SearchContactListService(
                $logger,
                new ContactListJsonRepository(
                    $appConfig->getPathToContactList(),
                    new JsonDataLoader()
                )
            )
        );

        //Act
        $httpResponse = $controller($httpRequest);
        $actualResult =  json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $expected = [
            [
                "id_recipient" => 1,
                "id_entry" => 1,
                "blacklist" => false
            ]
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
