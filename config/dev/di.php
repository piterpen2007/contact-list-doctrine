<?php

use EfTech\ContactList\ConsoleCommand\FindContacts;
use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\Controller\GetContactCollectionController;
use EfTech\ContactList\Controller\GetContactController;
use EfTech\ContactList\Controller\GetContactListCollectionController;
use EfTech\ContactList\Controller\GetContactListController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetCustomersController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Controller\GetRecipientsController;
use EfTech\ContactList\Controller\UpdateMoveToBlacklistContactListController;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use EfTech\ContactList\Entity\ContactRepositoryInterface;
use EfTech\ContactList\Entity\CustomerRepositoryInterface;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
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
use EfTech\ContactList\Repository\ContactJsonRepository;
use EfTech\ContactList\Repository\ContactListJsonRepository;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\MoveToBlacklistContactListService;
use EfTech\ContactList\Service\SearchContactListService;
use EfTech\ContactList\Service\SearchContactsService;
use EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService;

return [
    'instances' => [
        'handlers' => require __DIR__ . '/../request.handlers.php',
        'regExpHandlers' => require __DIR__ . '/../regExp.handlers.php',
        'controllerNs' => 'EfTech\\ContactList\\Controller',
        'appConfig' => require __DIR__ . '/config.php'
    ],
    'services' => [
        UpdateMoveToBlacklistContactListController::class => [
            'args' => [
                'moveToBlacklistContactListService' => MoveToBlacklistContactListService::class
            ]
        ],
        MoveToBlacklistContactListService::class => [
            'args' => [
                'textDocumentRepository' => ContactListRepositoryInterface::class
            ]
        ],
        ContactListRepositoryInterface::class => [
            'class' => ContactListJsonRepository::class,
            'args' => [
                'pathToContactList' => 'pathToContactList',
                'dataLoader' => DataLoaderInterface::class
            ]
        ],
        ContactRepositoryInterface::class => [
            'class' => ContactJsonRepository::class,
            'args' => [
                'pathToRecipients' => 'pathToRecipients',
                'pathToCustomers' => 'pathToCustomers',
                'pathToKinsfolk' => 'pathToKinsfolk',
                'pathToColleagues' => 'pathToColleagues',
                'dataLoader' => DataLoaderInterface::class
            ]
        ],
        CustomerRepositoryInterface::class => [
            'class' => CustomerJsonFileRepository::class,
            'args' => [
                'pathToCustomers' => 'pathToCustomers',
                'dataLoader' => DataLoaderInterface::class
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
        GetContactListCollectionController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchContactsService' => SearchContactListService::class,
            ]
        ],
        GetContactListController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchContactListService' => SearchContactListService::class,
            ]
        ],
        GetContactCollectionController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchContactsService' => SearchContactsService::class,
            ]
        ],
        SearchContactListService::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'contactListRepository' => ContactListRepositoryInterface::class,
            ]
        ],
        SearchContactsService::class => [
            'args' => [
                'contactRepository' => ContactRepositoryInterface::class,
                'logger' => LoggerInterface::class
            ]
        ],
        SearchRecipientsService::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'recipientRepository' => RecipientRepositoryInterface::class
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
                'customerRepository' => CustomerRepositoryInterface::class,
                'logger' => LoggerInterface::class
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
        FindContacts::class => [
            'args' => [
                'output' => OutputInterface::class,
                'searchContactsService' => SearchContactsService::class,
            ]
        ],
        GetContactController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchContactsService' => SearchContactsService::class,
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
        'pathToContactList' => static function (ContainerInterface $c): string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToContactList();
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