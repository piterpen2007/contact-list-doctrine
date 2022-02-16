<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetAddressCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Repository\AddressJsonFileRepository;
use EfTech\ContactList\Service\SearchAddressService;
use Exception;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GetAddressCollectionControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchAddress(): void
    {
        //Arrange
        $httpRequest = new \Nyholm\Psr7\ServerRequest(
            'GET',
            new \Nyholm\Psr7\Uri('http://localhost:8082/address?id_address=2'),
            ['Content-Type' => 'application/json'],
        );
        $queryParams = [];
        parse_str($httpRequest->getUri()->getQuery(), $queryParams);
        $httpRequest = $httpRequest->withQueryParams($queryParams);
        $psr17Factory = new Psr17Factory();
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new NullLogger();

        $controller = new GetAddressCollectionController(
            $logger,
            new SearchAddressService(
                $logger,
                new AddressJsonFileRepository(
                    $appConfig->getPathToAddresses(),
                    new JsonDataLoader()
                )
            ),
            new ServerResponseFactory($psr17Factory, $psr17Factory)
        );

        //Act
        $httpResponse = $controller($httpRequest);
        $actualResult =  json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $expected = [
            [
                "id_address" => 2,
                "id_recipient" => 1,
                "address" => "",
                "status" => ""
            ]
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
