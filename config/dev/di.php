<?php


use EfTech\ContactList\Controller\FindContactOnCategory;
use EfTech\ContactList\Controller\FindCustomers;
use EfTech\ContactList\Controller\FindRecipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\Logger\FileLogger\Logger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
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
                'appConfig' => AppConfig::class,
                'logger' => LoggerInterface::class
            ]
        ],
        FindCustomers::class => [
            'args' => [
                'appConfig' => AppConfig::class,
                'logger' => LoggerInterface::class
            ]
        ],
        FindContactOnCategory::class => [
            'args' => [
                'appConfig' => AppConfig::class,
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
        ]
    ],


    'factories' => [
        'pathToLogFile' => static function (ContainerInterface $c): string {
            /** @var AppConfig $appConfig */
            $appConfig = $c->get(AppConfig::class);
            return $appConfig->getPathToLogFile();
        },
        AppConfig::class => static function (ContainerInterface $c): AppConfig {
            $appConfig = $c->get('appConfig');
            return AppConfig::createFromArray($appConfig);
        }
    ],
];