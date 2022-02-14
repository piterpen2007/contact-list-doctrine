<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetContactCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Repository\ContactJsonRepository;
use EfTech\ContactList\Service\SearchContactsService;
use Exception;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GetContactCollectionControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchContact(): void
    {
        //Arrange
        $httpRequest = new ServerRequest(
            'GET',
            new  Uri('http://localhost:8082/contact?category=customers'),
            ['Content-Type' => 'application/json'],
        );
        $queryParams = [];
        parse_str($httpRequest->getUri()->getQuery(), $queryParams);
        $httpRequest = $httpRequest->withQueryParams($queryParams);
        $psr17Factory = new Psr17Factory();
        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new NullLogger();

        $controller = new GetContactCollectionController(
            $logger,
            new SearchContactsService(
                new ContactJsonRepository(
                    $appConfig->getPathToRecipients(),
                    $appConfig->getPathToCustomers(),
                    $appConfig->getPathToKinsfolk(),
                    $appConfig->getPathToColleagues(),
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
            [
                "id_recipient" => 7,
                "full_name" => "Калинин Пётр Александрович",
                "birthday" => "04.06.1983",
                "profession" => "Фитнес тренер",
                "contract_number" => "5684",
                "average_transaction_amount" => 2500,
                "discount" => "5%",
                "time_to_call" => "С 9:00 до 13:00 в будни",
            ],
            [
                "id_recipient" => 8,
                "full_name" => "Васин Роман Александрович",
                "birthday" => "04.01.1977",
                "profession" => "Фитнес тренер",
                "contract_number" => "5683",
                "average_transaction_amount" => 9500,
                "discount" => "10%",
                "time_to_call" => "С 12:00 до 16:00 в будни",
            ],
            [
                "id_recipient" => 9,
                "full_name" => "Стрелецкая Анастасия Виктоовна",
                "birthday" => "30.12.1980",
                "profession" => "Админимстратор фитнес центра",
                "contract_number" => "5682",
                "average_transaction_amount" => 15200,
                "discount" => "10%",
                "time_to_call" => "С 17:00 до 19:00 в будни",
            ]
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
