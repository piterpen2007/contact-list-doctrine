<?php

require_once __DIR__ . '/../src/Infrastructure/Autoloader/Autoloader.php';

use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Infrastructure\Autoloader\Autoloader;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Infrastructure\View\NullRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use EfTech\ContactListTest\TestUtils;


spl_autoload_register(
    new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../test/'
    ])
);
/**
 *  Тестирование приложения
 */
class UnitTest
{
    private static function testDataProvider():array
    {
        $diConfig = require __DIR__ . '/../config/dev/di.php';
        $diConfig['services'][\EfTech\ContactList\Infrastructure\Logger\AdapterInterface::class] = [
            'class' => \EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter::class
        ];
        $diConfig['services'][RenderInterface::class] = [
            'class' => NullRender::class
        ];
        return [
            [
                'testName'=>'Тестирование поиска получателя по id',
                'in' => [
                    'uri' => '/recipient?id_recipient=1',
                    'diConfig' => $diConfig
                    ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                        'id_recipient' => 1,
                        'full_name' => 'Осипов Геннадий Иванович',
                        'birthday' => '15.06.1985',
                        'profession' => 'Системный администратор'
                        ],
                    ],
                ]
            ],
            [
                'testName' => 'Тестирование ситуации когда данные о получателях не кореектны. Нет поля birthday',
                'in' => [
                    'uri' => '/recipient?full_name=Осипов Геннадий Иванович',
                    'diConfig' => (static function($diConfig) {
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToRecipients'] = __DIR__ . '/data/broken.recipient.json';
                        $diConfig['instances']['appConfig'] = $config;
                        return $diConfig;
                    })($diConfig)
                ],
                'out' => [
                    'httpCode' => 503,
                    'result' => [
                        'status' => 'fail',
                        'message' => 'Отсутствуют обязательные элементы: birthday'
                    ]
                ]
            ],
            [
                'testName' => 'Тестирование ситуации с некорректным  данными конфига приложения',
                'in' => [
                    'uri' => '/recipient?id_recipient=1',
                    'diConfig' => (static function($diConfig) {
                        $diConfig['factories'][AppConfig::class] = static function () {
                            return 'Ops!';
                        };
                        return $diConfig;
                    })($diConfig)
                ],
                'out' => [
                    'httpCode' => 500,
                    'result' => [
                        'status' => 'fail',
                        'message' => 'system error'
                    ]
                ]
            ],
            [
                'testName' => 'Тестирование ситуации с некорректным путем до файла получателями',
                'in' => [
                    'uri' =>  '/recipient?id_recipient=1',
                    'diConfig' => (static function($diConfig) {
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToRecipients'] = __DIR__ . '/data/unknown.recipients.json';
                        $diConfig['instances']['appConfig'] = $config;
                        return $diConfig;
                    })($diConfig)
                ],
                'out' => [
                    'httpCode' => 500,
                    'result' => [
                        'status' => 'fail',
                        'message' => 'Некорректный путь до файла с данными'
                    ]
                ]
            ],
            [
                'testName' => 'Тестирование ситуации когда данные о клиентах некорректны. Нет поля id_recipient',
                'in' => [
                    'uri' => '/customers?full_name=Калинин Пётр Александрович',
                    'diConfig' => (static function($diConfig) {
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToCustomers'] = __DIR__ . '/data/broken.customers.json';
                        $diConfig['instances']['appConfig'] = $config;
                        return $diConfig;
                    })($diConfig)
                ],
                'out' => [
                    'httpCode' => 503,
                    'result' => [
                        'status' => 'fail',
                        'message' => 'Отсутствуют обязательные элементы: id_recipient'
                    ]
                ]
            ,

        ],
        ];
    }

    /**
     * Запускает тест
     *
     * @return void
     * @throws JsonException
     */
    public static function runTest():void
    {
        foreach (static::testDataProvider() as $testItem) {
            echo "-----{$testItem['testName']}-----\n";

            $httpRequest = new ServerRequest(
                'GET',
                '1.1',
                $testItem['in']['uri'],
                Uri::createFromString($testItem['in']['uri']),
                ['Content-Type' => 'application/json'],
                null
            );
            //Arrange и Act

            $diConfig = $testItem['in']['diConfig'];
            $httpResponse = (new App(
                static function(Container $di): RouterInterface {return $di->get(RouterInterface::class);},
                static function(Container $di):LoggerInterface {return $di->get(LoggerInterface::class);},
                static function(Container $di):AppConfig {return $di->get(AppConfig::class);},
                static function(Container $di):RenderInterface {return $di->get(RenderInterface::class);},
                static function() use($diConfig) :Container {return Container::createFromArray($diConfig);}
            ))->dispath($httpRequest);


            //Assert
            if ($httpResponse->getStatusCode() === $testItem['out']['httpCode']) {
                echo "    OK --- код ответа\n";
            } else {
                echo "    FAIL - код ответа. Ожидалось: {$testItem['out']['httpCode']}. Актуальное значение: {$httpResponse->getStatusCode()}\n";
            }

            $actualResult =  json_decode($httpResponse->getBody(), true, 512 , JSON_THROW_ON_ERROR);

            $unnecessaryElements = TestUtils::arrayDiffAssocRecursive($actualResult, $testItem['out']['result']);
            $missingElements =  TestUtils::arrayDiffAssocRecursive($testItem['out']['result'], $actualResult);

            $errMsg = '';

            if (count($unnecessaryElements) > 0) {
                $errMsg .= sprintf("         Есть лишние элементы %s\n", json_encode($unnecessaryElements,JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            }
            if (count($missingElements) > 0) {
                $errMsg .= sprintf("         Есть лишние недостающие элементы %s\n", json_encode($missingElements,JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            }

            if ('' === $errMsg) {
                echo "    ОК- данные ответа валидны\n";
            } else {
                echo "    FAIL - данные ответа валидны\n" . $errMsg;
            }


        }
    }
}
UnitTest::runTest();