<?php

namespace EfTech\ContactListTest;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Config\ContainerExtensions;
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
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use EfTech\ContactList\Infrastructure\Router\ChainRouters;
use EfTech\ContactList\Infrastructure\Router\DefaultRouter;
use EfTech\ContactList\Infrastructure\Router\RegExpRouter;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Router\UniversalRouter;
use EfTech\ContactList\Infrastructure\View\DefaultRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 *  Тестирование создания сервисов приложения
 */
class DiAppServiceTest extends TestCase
{
    /**
     *
     *
     * @return array
     */
    public static function serviceDataProvider(): array
    {
        return [
            HashStr::class => [
                '$serviceId' => HashStr::class,
                '$expectedServiceClass' => HashStr::class
            ],
            AppConfig::class => [
                'serviceId' => AppConfig::class,
                'expectedServiceClass' => AppConfig::class
            ],
            LoginController::class => [
                'serviceId' => LoginController::class,
                'expectedServiceClass' => LoginController::class
            ],
            AddressAdministrationController::class => [
                'serviceId' => AddressAdministrationController::class,
                'expectedServiceClass' => AddressAdministrationController::class
            ],
            CreateAddressController::class => [
                'serviceId' => CreateAddressController::class,
                'expectedServiceClass' => CreateAddressController::class
            ],
            UpdateMoveToBlacklistContactListController::class => [
                'serviceId' => UpdateMoveToBlacklistContactListController::class,
                'expectedServiceClass' => UpdateMoveToBlacklistContactListController::class
            ],
            GetAddressCollectionController::class => [
                'serviceId' => GetAddressCollectionController::class,
                'expectedServiceClass' => GetAddressCollectionController::class
            ],
            GetAddressController::class => [
                'serviceId' => GetAddressController::class,
                'expectedServiceClass' => GetAddressController::class
            ],
            GetContactListCollectionController::class => [
                'serviceId' => GetContactListCollectionController::class,
                'expectedServiceClass' => GetContactListCollectionController::class
            ],
            GetContactListController::class => [
                'serviceId' => GetContactListController::class,
                'expectedServiceClass' => GetContactListController::class
            ],
            GetRecipientsController::class => [
                'serviceId' => GetRecipientsController::class,
                'expectedServiceClass' => GetRecipientsController::class
            ],
            GetRecipientsCollectionController::class => [
                'serviceId' => GetRecipientsCollectionController::class,
                'expectedServiceClass' => GetRecipientsCollectionController::class
            ],
            GetCustomersCollectionController::class => [
                'serviceId' => GetCustomersCollectionController::class,
                'expectedServiceClass' => GetCustomersCollectionController::class
            ],
            GetCustomersController::class => [
                'serviceId' => GetCustomersController::class,
                'expectedServiceClass' => GetCustomersController::class
            ],
            FindCustomers::class => [
                'serviceId' => FindCustomers::class,
                'expectedServiceClass' => FindCustomers::class
            ],
            FindRecipients::class => [
                'serviceId' => FindRecipients::class,
                'expectedServiceClass' => FindRecipients::class
            ],
            DefaultRouter::class => [
                'serviceId' => DefaultRouter::class,
                'expectedServiceClass' => DefaultRouter::class
            ],
            RegExpRouter::class => [
                'serviceId' => RegExpRouter::class,
                'expectedServiceClass' => RegExpRouter::class
            ],
            UniversalRouter::class => [
                'serviceId' => UniversalRouter::class,
                'expectedServiceClass' => UniversalRouter::class
            ],
            RouterInterface::class => [
                'serviceId' => RouterInterface::class,
                'expectedServiceClass' => ChainRouters::class
            ],
            RenderInterface::class => [
                'serviceId' => RenderInterface::class,
                'expectedServiceClass' => DefaultRender::class
            ]
        ];
    }
    /** Проверяет корректность создания сервиса через di контейнер
     *
     * @dataProvider serviceDataProvider
     * @runInSeparateProcess
     * @param string $serviceId
     * @param string $expectedServiceClass
     * @throws Exception
     */
    public function testCreateService(string $serviceId, string $expectedServiceClass): void
    {
        //Arrange
        $diContainerFactory = new SymfonyDiContainerInit(
            new SymfonyDiContainerInit\ContainerParams(
                __DIR__ . '/../config/dev/di.xml',
                [
                    'kernel.project_dir' => __DIR__ . '/../'
                ],
                ContainerExtensions::httpAppContainerExtension()
            )
        );
        $diContainer = $diContainerFactory();

        //Act
        $actualService = $diContainer->get($serviceId);

        //Assert
        $this->assertInstanceOf($expectedServiceClass, $actualService);
    }
}
