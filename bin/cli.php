#!/usr/nin/env_php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EfTech\ContactList\Infrastructure\Console\AppConsole;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;


(new AppConsole(
    require __DIR__ . '/../config/console.handlers.php',
    static function (ContainerInterface $di): OutputInterface {
        return $di->get(OutputInterface::class);
    },
    new SymfonyDiContainerInit(
        __DIR__ . '/../config/dev/di.xml',
        [
            'kernel.project_dir' => __DIR__ . '/../'
        ]
    )
))->dispatch();
