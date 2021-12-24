<?php
use EfTech\ContactList\Controller\FindContactOnCategory;
use EfTech\ContactList\Controller\FindCustomers;
use EfTech\ContactList\Controller\FindRecipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\Logger\FileLogger\Logger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\ChainRouters;
use EfTech\ContactList\Infrastructure\Router\ControllerFactory;
use EfTech\ContactList\Infrastructure\Router\DefaultRouter;
use EfTech\ContactList\Infrastructure\Router\RegExpRouter;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\View\DefaultRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;

return [
    'instances' => [
        'handlers' => require __DIR__ . '/../request.handlers.php',
        'appConfig' => require __DIR__ . '/config.php'
    ],
    'services' => [
        FindRecipient::class => [
            'args' => [
                'pathToRecipients' => 'pathToRecipients',
                'logger' => LoggerInterface::class
            ]
        ],
        FindCustomers::class => [
            'args' => [
                'pathToCustomers' => 'pathToCustomers',
                'logger' => LoggerInterface::class
            ]
        ],
        FindContactOnCategory::class => [
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
                DefaultRouter::class
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

            ]
        ]
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