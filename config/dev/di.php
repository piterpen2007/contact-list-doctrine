<?php

use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\Controller\GetContactCollectionController;
use EfTech\ContactList\Controller\GetContactController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetCustomersController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Controller\GetRecipientsController;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Console\Output\EchoOutput;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\Logger\FileLogger\Logger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\ChainRouters;
use EfTech\ContactList\Infrastructure\Router\ControllerFactory;
use EfTech\ContactList\Infrastructure\Router\DefaultRouter;
use EfTech\ContactList\Infrastructure\Router\RegExpRouter;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Router\UniversalRouter;
use EfTech\ContactList\Infrastructure\View\DefaultRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use EfTech\ContactList\Service\SearchCustomersService\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsService;

return [
    'instances' => [
        'handlers' => require __DIR__ . '/../request.handlers.php',
        'regExpHandlers' => require __DIR__ . '/../regExp.handlers.php',
        'controllerNs' => 'EfTech\\ContactList\\Controller',
        'appConfig' => require __DIR__ . '/config.php'
    ],
    'services' => [
        DataLoaderInterface::class => [
            'class' => JsonDataLoader::class
        ],
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

        GetRecipientsController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchRecipientsService' => SearchRecipientsService::class,
            ]
        ],
        SearchCustomersService::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'pathToCustomers' => 'pathToCustomers',
                'dataLoader' => DataLoaderInterface::class
            ]
        ],
        GetCustomersController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchCustomersService' => SearchCustomersService::class,
            ]
        ],
        GetCustomersCollectionController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchCustomersService' => SearchCustomersService::class,
                ]
        ],
        FindCustomers::class => [
            'args' => [
                'output' => OutputInterface::class,
                'searchCustomersService' => SearchCustomersService::class,
            ]
        ],
        FindRecipients::class => [
            'args' => [
                'output' => OutputInterface::class,
                'searchRecipientsService' => SearchRecipientsService::class,
            ]
        ],
        GetContactCollectionController::class => [
            'args' => [
                'pathToCustomers' => 'pathToCustomers',
                'pathToRecipients' => 'pathToRecipients',
                'pathToKinsfolk' => 'pathToKinsfolk',
                'pathToColleagues' => 'pathToColleagues',
                'logger' => LoggerInterface::class

            ]
        ],
        GetContactController::class => [
            'args' => [
                'pathToCustomers' => 'pathToCustomers',
                'pathToRecipients' => 'pathToRecipients',
                'pathToKinsfolk' => 'pathToKinsfolk',
                'pathToColleagues' => 'pathToColleagues',
                'logger' => LoggerInterface::class

            ]
        ],
        LoggerInterface::class => [
            'class' => Logger::class,
            'args' => [
                'pathToFile' => 'pathToLogFile'
            ]
        ],
        RenderInterface::class => [
            'class' => DefaultRender::class
        ],
        RouterInterface::class => [
            'class' => ChainRouters::class,
            'args' => [
                RegExpRouter::class,
                DefaultRouter::class,
                UniversalRouter::class
            ]
        ],
        UniversalRouter::class => [
            'args' => [
                'ControllerFactory' => ControllerFactory::class,
                'controllerNs' => 'controllerNs'
            ]
        ],
        DefaultRouter::class => [
            'args' => [
                'handlers' => 'handlers',
                'controllerFactory' => ControllerFactory::class
            ]
        ],
        ControllerFactory::class => [
            'args' => [
                'diContainer' => ContainerInterface::class
            ]
        ],
        RegExpRouter::class => [
            'args' => [
                'handlers' => 'regExpHandlers',
                'controllerFactory' => ControllerFactory::class
            ]
        ],
        OutputInterface::class => [
            'class' => EchoOutput::class,
            'args' => [

            ]
        ],
    ],


    'factories' => [
        ContainerInterface::class => static function(ContainerInterface $c):ContainerInterface {
            return $c;
        },
        'pathToLogFile' => static function (ContainerInterface $c): string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToLogFile();
        },
        'pathToCustomers' => static function(ContainerInterface $c):string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToCustomers();
        },
        'pathToRecipients' => static function(ContainerInterface $c):string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToRecipients();
        },
        'pathToKinsfolk' => static function(ContainerInterface $c):string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToKinsfolk();
        },
        'pathToColleagues' => static function(ContainerInterface $c):string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToColleagues();
        },
        AppConfig::class => static function (ContainerInterface $c): AppConfig {
            $appConfig = $c->get('appConfig');
            return AppConfig::createFromArray($appConfig);
        }
    ],
];