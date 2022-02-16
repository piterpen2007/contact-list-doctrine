<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetCustomersController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Service\SearchCustomersService;
use Exception;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GetCustomersControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchCustomersForFullName(): void
    {
        //Arrange
        $httpRequest = new \Nyholm\Psr7\ServerRequest(
            'GET',
            new \Nyholm\Psr7\Uri('http://localhost:8082/customers?full_name=Васин Роман Александрович'),
            ['Content-Type' => 'application/json'],
        );

        $queryParams = [];
        parse_str($httpRequest->getUri()->getQuery(), $queryParams);
        $httpRequest = $httpRequest->withQueryParams($queryParams);

        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new NullLogger();
        $psr17Factory = new Psr17Factory();
        $controller = new GetCustomersController(
            $logger,
            new SearchCustomersService(
                new CustomerJsonFileRepository(
                    $appConfig->getPathToCustomers(),
                    new JsonDataLoader()
                ),
                $logger
            ),
            new ServerResponseFactory($psr17Factory, $psr17Factory)
        );

        //Act
        $httpResponse = $controller($httpRequest);
        $actualResult =  json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $expected = [
                "id_recipient" => 8,
                "full_name" => "Васин Роман Александрович",
                "birthday" => "04.01.1977",
                "profession" => "Фитнес тренер",
                "contract_number" => "5683",
                "average_transaction_amount" => 9500,
                "discount" => "10%",
                "time_to_call" => "С 12:00 до 16:00 в будни",
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
