<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetCustomersController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;

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
        $httpRequest = new ServerRequest(
            'GET',
            '1.1',
            '/customers?full_name=Васин Роман Александрович',
            Uri::createFromString('http://localhost:8082/customers?full_name=Васин Роман Александрович'),
            ['Content-Type' => 'application/json'],
            null
        );
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new Logger(new NullAdapter());

        $controller = new GetCustomersController(
            $logger,
            new SearchCustomersService(
                new CustomerJsonFileRepository(
                    $appConfig->getPathToCustomers(),
                    new JsonDataLoader()
                ),
                $logger
            )
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
