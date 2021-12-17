<?php

require_once __DIR__ . '/../src/Infrastructure/Autoloader.php';
require_once __DIR__ . '/../src/Infrastructure/app.function.php';

use EfTech\ContactList\Infrastructure\Autoloader;
spl_autoload_register(new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../test/'
    ])
);
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\App;
use EfTech\ContactList\Infrastructure\http\ServerRequestFactory;

use function EfTech\ContactList\Infrastructure\render;


$httpResponse = (new App(
    include __DIR__ . '/../config/request.handlers.php',
    'EfTech\ContactList\Infrastructure\Logger\Factory::create',
    static function() {return AppConfig::createFromArray(include __DIR__ . '/../config/dev/config.php');}
))->dispath(ServerRequestFactory::createFromGlobals($_SERVER,));

render($httpResponse);
