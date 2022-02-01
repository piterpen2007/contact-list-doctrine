<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\http\ServerRequestFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\View\RenderInterface;


$httpResponse = (new App(
    static function (Container $di): RouterInterface {
        return $di->get(RouterInterface::class);
    },
    static function (Container $di): LoggerInterface {
        return $di->get(LoggerInterface::class);
    },
    static function (Container $di): AppConfig {
        return $di->get(AppConfig::class);
    },
    static function (Container $di): RenderInterface {
        return $di->get(RenderInterface::class);
    },
    static function (): Container {
        return Container::createFromArray(require __DIR__ . '/../config/dev/di.php');
    }
))->dispath(ServerRequestFactory::createFromGlobals($_SERVER, file_get_contents('php://input')));
