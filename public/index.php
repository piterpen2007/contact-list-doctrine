<?php

use EfTech\ContactList\Infrastructure\AppConfig;
use function EfTech\ContactList\Infrastructure\app;
use function EfTech\ContactList\Infrastructure\render;

require_once __DIR__ . '/../src/Infrastructure/app.function.php';
require_once __DIR__ . '/../src/Infrastructure/AppConfig.php';
require_once __DIR__ . '/../src/Infrastructure/Logger/Factory.php';

$resultApp = app
(
    include __DIR__ . '/../config/request.handlers.php',
    $_SERVER['REQUEST_URI'],
        'EfTech\ContactList\Infrastructure\Logger\Factory::create',
    static function() {return AppConfig::createFromArray(include __DIR__ . '/../config/dev/config.php');}
);
render($resultApp['result'], $resultApp['httpCode']);