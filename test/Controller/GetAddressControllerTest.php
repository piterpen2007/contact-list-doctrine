<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetAddressCollectionController;
use EfTech\ContactList\Controller\GetAddressController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Repository\AddressJsonFileRepository;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchAddressService;
use EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;

class GetAddressControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchAddress(): void
    {
        //Arrange
        $httpRequest = new ServerRequest(
            'GET',
            '1.1',
            '/address?id_address=2',
            Uri::createFromString('http://localhost:8082/address?id_address=2'),
            ['Content-Type' => 'application/json'],
            null
        );
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new Logger(new NullAdapter());

        $controller = new GetAddressController(
            $logger,
            new SearchAddressService(
                $logger,
                new AddressJsonFileRepository(
                    $appConfig->getPathToAddresses(),
                    new JsonDataLoader()
                )
            )
        );

        //Act
        $httpResponse = $controller($httpRequest);
        $actualResult =  json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $expected = [
                "id_address" => 2,
                "id_recipient" => 1,
                "address" => "",
                "status" => ""
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
