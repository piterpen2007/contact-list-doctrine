<?php

namespace EfTech\ContactListTest;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Config\ContainerExtensions;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use Exception;
use PHPUnit\Framework\TestCase;

class DiAppConfigServiceTest extends TestCase
{
    /** Поставщик данных для теста
     * @return array[]
     */
    public static function appConfigDataProvider(): array
    {
        return [
            'pathToRecipients' => [
                'method' => 'getPathToRecipients',
                'expectedValue' => __DIR__ . '/../data/recipient.json',
                'isPath' => true
            ],
            'pathToKinsfolk' => [
                'method' => 'getPathToKinsfolk',
                'expectedValue' => __DIR__ . '/../data/kinsfolk.json',
                'isPath' => true
            ],
            'pathToCustomers' => [
                'method' => 'getPathToCustomers',
                'expectedValue' => __DIR__ . '/../data/customers.json',
                'isPath' => true
            ],
            'pathToColleagues' => [
                'method' => 'getPathToColleagues',
                'expectedValue' => __DIR__ . '/../data/colleagues.json',
                'isPath' => true
            ],
            'pathToContactList' => [
                'method' => 'getPathToContactList',
                'expectedValue' => __DIR__ . '/../data/contact_list.json',
                'isPath' => true
            ],
            'pathToAddresses' => [
                'method' => 'getPathToAddresses',
                'expectedValue' => __DIR__ . '/../data/address.json',
                'isPath' => true
            ],
            'pathToLogFile' => [
                'method' => 'getPathToLogFile',
                'expectedValue' => __DIR__ . '/../var/log/app.log',
                'isPath' => true
            ],
            'pathToUsers' => [
                'method' => 'getPathToUsers',
                'expectedValue' => __DIR__ . '/../data/users.json',
                'isPath' => true
            ],
            'loginUri' => [
                'method' => 'getLoginUri',
                'expectedValue' => '/login',
                'isPath' => false
            ],
            'hideErrorMsg' => [
                'method' => 'isHideErrorMsg',
                'expectedValue' => false,
                'isPath' => false
            ],
        ];
    }

    /** Тестирование получения значений из конфига приложений
     *
     *
     * @dataProvider appConfigDataProvider
     * @param string $method
     * @param $expectedValue
     * @param bool $isPath
     * @throws Exception
     */
    public function testAppConfigGetter(string $method, $expectedValue, bool $isPath): void
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
        $appConfig = $diContainer->get(AppConfig::class);

        //Act
        $actualValue = $appConfig->$method();

        //Assert
        if ($isPath) {
            $expectedValue = realpath($expectedValue);
            $actualValue = realpath($actualValue);
        }
        $this->assertSame($actualValue, $expectedValue);
    }
}
