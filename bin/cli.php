#!/usr/nin/env_php
<?php
use EfTech\ContactList\Infrastructure\Autoloader\Autoloader;
use EfTech\ContactList\Infrastructure\Console\AppConsole;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;

require_once __DIR__ . '/../src/Infrastructure/Autoloader/Autoloader.php';

spl_autoload_register(new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../test/'
    ])
);

(new AppConsole(
    require __DIR__ . '/../config/console.handlers.php',
    static function(ContainerInterface $di):OutputInterface {
        return $di->get(OutputInterface::class);
    },
    static function():ContainerInterface {
        return Container::createFromArray(require __DIR__ . '/../config/dev/di.php');
    }
))->dispatch();