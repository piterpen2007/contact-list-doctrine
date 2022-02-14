<?php

namespace EfTech\ContactListTest\Infrastructure\Controller;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchRecipientsService;
use Exception;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GetRecipientCollectionControllerTest extends TestCase
{
    /** Тестирование поиска получателей по фамилии
     *
     * @throws JsonException
     * @throws Exception
     */
    public function testSearchRecipientForFullName(): void
    {
        //Arrange
        $httpRequest = new ServerRequest(
            'GET',
            new Uri('http://localhost:8082/recipient?full_name=Осипов Геннадий Иванович'),
            ['Content-Type' => 'application/json'],
        );

        $queryParams = [];
        parse_str($httpRequest->getUri()->getQuery(), $queryParams);
        $httpRequest = $httpRequest->withQueryParams($queryParams);

        $appConfig = AppConfig::createFromArray(require __DIR__ . '/../../config/dev/config.php');
        $logger = new NullLogger();
        $psr17Factory = new Psr17Factory();
        $controller = new GetRecipientsCollectionController(
            $logger,
            new SearchRecipientsService(
                $logger,
                new RecipientJsonFileRepository(
                    $appConfig->getPathToRecipients(),
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
                'id_recipient' => 1,
                'full_name' => 'Осипов Геннадий Иванович',
                'birthday' => '15.06.1985',
                'profession' => 'Системный администратор',
            ]
        ];

        //Assert
        $this->assertEquals(200, $httpResponse->getStatusCode(), 'код http ответа не корректен');
        $this->assertEquals($expected, $actualResult, 'Данные ответа не валидны');
    }
}
