<?php

namespace EfTech\ContactListTest\Infrastructure\DI;

use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Autoloader;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\Logger\FileLogger\Logger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsService;


require_once __DIR__ . '/../../src/Infrastructure/Autoloader.php';

spl_autoload_register(
    new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../../test/'
    ])
);
class ContainerTest
{
    /**
     * Тестирование получения сервиса
     */
    public static function testGetService():void
    {
        echo "------------------Тестирование получения сервиса---------------\n";
        //Arrange
        $diConfig = [
            'instances'=> [
                'appConfig' =>require __DIR__ . '/../../config/dev/config.php'
            ],
            'services' => [
                SearchRecipientsService::class => [
                    'args' => [
                        'logger' => LoggerInterface::class,
                        'pathToRecipients' => 'pathToRecipients',
                        'dataLoader' => DataLoaderInterface::class
                    ]

                ],
                GetRecipientsCollectionController::class => [
                    'args' => [
                        'logger' => LoggerInterface::class,
                        'searchRecipientsService' => SearchRecipientsService::class,
                    ]
                ],
                LoggerInterface::class => [
                    'class' => Logger::class,
                    'args' => [
                        'pathToFile' => 'pathToLogFile'
                    ]
                ],
                DataLoaderInterface::class => [
                    'class' => JsonDataLoader::class
                ],


            ],
            'factories' => [
                'pathToLogFile' => static function(ContainerInterface $c):string {
                    /** @var AppConfig $appConfig */
                    $appConfig = $c->get(AppConfig::class);
                    return $appConfig->getPathToLogFile();
                },
                AppConfig::class => static function(ContainerInterface $c): AppConfig {
                    $appConfig = $c->get('appConfig');
                    return AppConfig::createFromArray($appConfig);
                },
                'pathToRecipients' => static function(ContainerInterface $c):string {
                    /** @var AppConfig $appConfig */
                    $appConfig = $c->get(AppConfig::class);
                    return $appConfig->getPathToRecipients();
                },

            ]

        ];
        $di = Container::createFromArray($diConfig);
        //Act
        $controller = $di->get(GetRecipientsCollectionController::class);

        //Assert
        if ($controller instanceof GetRecipientsCollectionController) {
            echo "     ОК - di контейнер отработал корректно";
        } else {
            echo "     FAIL - di контейнер отработал корректно";
        }
    }

}

ContainerTest::testGetService();