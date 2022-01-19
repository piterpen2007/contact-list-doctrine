<?php

use EfTech\ContactList\Infrastructure\Logger\Adapter\FileAdapter;
use EfTech\ContactList\Infrastructure\Logger\AdapterInterface;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Infrastructure\Session\SessionNative;
use EfTech\ContactList\ConsoleCommand\FindContacts;
use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\ConsoleCommand\HashStr;
use EfTech\ContactList\Controller\AddressAdministrationController;
use EfTech\ContactList\Controller\CreateAddressController;
use EfTech\ContactList\Controller\GetAddressCollectionController;
use EfTech\ContactList\Controller\GetAddressController;
use EfTech\ContactList\Controller\GetContactCollectionController;
use EfTech\ContactList\Controller\GetContactController;
use EfTech\ContactList\Controller\GetContactListCollectionController;
use EfTech\ContactList\Controller\GetContactListController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetCustomersController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;
use EfTech\ContactList\Controller\GetRecipientsController;
use EfTech\ContactList\Controller\LoginController;
use EfTech\ContactList\Controller\UpdateMoveToBlacklistContactListController;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use EfTech\ContactList\Entity\ContactRepositoryInterface;
use EfTech\ContactList\Entity\CustomerRepositoryInterface;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Auth\HttpAuthProvider;
use EfTech\ContactList\Infrastructure\Auth\UserDataStorageInterface;
use EfTech\ContactList\Infrastructure\Console\Output\EchoOutput;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\ChainRouters;
use EfTech\ContactList\Infrastructure\Router\ControllerFactory;
use EfTech\ContactList\Infrastructure\Router\DefaultRouter;
use EfTech\ContactList\Infrastructure\Router\RegExpRouter;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Router\UniversalRouter;
use EfTech\ContactList\Infrastructure\Session\SessionInterface;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Infrastructure\View\DefaultRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use EfTech\ContactList\Infrastructure\ViewTemplate\PhtmlTemplate;
use EfTech\ContactList\Infrastructure\ViewTemplate\ViewTemplateInterface;
use EfTech\ContactList\Repository\AddressJsonFileRepository;
use EfTech\ContactList\Repository\ContactJsonRepository;
use EfTech\ContactList\Repository\ContactListJsonRepository;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Repository\UserJsonFileRepository;
use EfTech\ContactList\Service\ArrivalAddressService;
use EfTech\ContactList\Service\MoveToBlacklistContactListService;
use EfTech\ContactList\Service\SearchAddressService;
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
        HashStr::class => [
            'args' => [
                'output' => OutputInterface::class
            ]
        ],
        HttpAuthProvider::class => [
            'args' => [
                'userDataStorage' => UserDataStorageInterface::class,
                'session' => SessionInterface::class,
                'loginUri' => 'loginUri'
            ]
        ],
        LoginController::class => [
            'args' => [
                'viewTemplate' => ViewTemplateInterface::class,
                'httpAuthProvider' => HttpAuthProvider::class
            ]
        ],
        UserDataStorageInterface::class => [
            'class' => UserJsonFileRepository::class,
            'args' => [
                'pathToUsers' => 'pathToUsers',
                'dataLoader' => DataLoaderInterface::class
            ]
        ],
        ViewTemplateInterface::class => [
            'class' => PhtmlTemplate::class
        ],
        AddressAdministrationController::class => [
            'args' => [
                'arrivalAddressService' => ArrivalAddressService::class,
                'searchAddressService' => SearchAddressService::class,
                'viewTemplate' => ViewTemplateInterface::class,
                'logger' => LoggerInterface::class,
                'searchContactsService' => SearchContactsService::class,
                'httpAuthProvider' => HttpAuthProvider::class
            ]
        ],
        CreateAddressController::class => [
            'args' => [
                'arrivalAddressService' => ArrivalAddressService::class
            ]
        ],
        ArrivalAddressService::class => [
            'args' => [
                'addressRepositoryInterface' => AddressRepositoryInterface::class
            ]
        ],
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
        GetAddressCollectionController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchAddressService' => SearchAddressService::class,
            ]
        ],
        GetAddressController::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'searchAddressService' => SearchAddressService::class,
            ]
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
        SearchAddressService::class => [
            'args' => [
                'logger' => LoggerInterface::class,
                'contactListRepository' => AddressRepositoryInterface::class,
            ]
        ],
        AddressRepositoryInterface::class => [
            'class' => AddressJsonFileRepository::class,
            'args' => [
                'pathToAddresses' => 'pathToAddresses',
                'dataLoader' => DataLoaderInterface::class
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
                'adapter' => AdapterInterface::class
            ]
        ],
        AdapterInterface::class => [
            'class' => FileAdapter::class,
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
        'loginUri' => static function(ContainerInterface $c): Uri {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return Uri::createFromString($appConfig->getLoginUri());
        },
        SessionInterface::class => static function(ContainerInterface $c) {
            return SessionNative::create();
        },
        ContainerInterface::class => static function(ContainerInterface $c):ContainerInterface {
            return $c;
        },
        'pathToUsers' => static function(ContainerInterface $c):string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToUsers();
        },
        'pathToAddresses' => static function (ContainerInterface $c): string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToAddresses();
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