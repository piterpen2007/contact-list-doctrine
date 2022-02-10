<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Config\ContainerExtensions;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Infrastructure\http\ServerRequestFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\View\RenderInterface;


$httpResponse = (new App(
    static function (ContainerInterface $di): RouterInterface {
        return $di->get(RouterInterface::class);
    },
    static function (ContainerInterface $di): LoggerInterface {
        return $di->get(LoggerInterface::class);
    },
    static function (ContainerInterface $di): AppConfig {
        return $di->get(AppConfig::class);
    },
    static function (ContainerInterface $di): RenderInterface {
        return $di->get(RenderInterface::class);
    },
    new SymfonyDiContainerInit(
        new SymfonyDiContainerInit\ContainerParams(
            __DIR__ . '/../config/dev/di.xml',
            [
                'kernel.project_dir' => __DIR__ . '/../'
            ],
            ContainerExtensions::httpAppContainerExtension()
        ),
        new SymfonyDiContainerInit\CacheParams(
            'DEV' !== getenv('ENV_TYPE'),
            __DIR__ . '/../var/cache/di-symfony/EfTechContactListCachedContainer.php'
        )
    )
))->dispath(ServerRequestFactory::createFromGlobals($_SERVER, file_get_contents('php://input')));
