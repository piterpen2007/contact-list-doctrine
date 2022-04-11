<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EfTech\ContactList\Config\ContainerExtensions;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;


require_once __DIR__ . '/../vendor/autoload.php';

$container = (new SymfonyDiContainerInit(
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
))();

$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);


return ConsoleRunner::createHelperSet($entityManager);

