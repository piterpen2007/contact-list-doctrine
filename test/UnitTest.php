<?php

require_once __DIR__ . '/../src/Infrastructure/app.function.php';
require_once __DIR__ . '/../src/Infrastructure/Autoloader.php';

use EfTech\ContactList\Infrastructure\App;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Autoloader;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Logger\NullLogger\Logger;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactListTest\TestUtils;


spl_autoload_register(
    new Autoloader([
        'EfTech\\ContactList\\' => __DIR__ . '/../src/',
        'EfTech\\ContactListTest\\' => __DIR__ . '/../test/'
    ])
);


/** Вычисляет расскхождение массивов с доп проверкой индекса. Поддержка многомерных массивов
 * @param array $a1
 * @param array $a2
 * @return array
 */
function array_diff_assoc_recursive(array $a1,array $a2):array
{
    $result = [];
    foreach ($a1 as $k1 => $v1) {
        if(false === array_key_exists($k1, $a2)){
            $result[$k1] = $v1;
            continue;
        }
        if(is_iterable($v1) && is_iterable($a2[$k1])) {
            $resultCheck = array_diff_assoc_recursive($v1, $a2[$k1]);
            if (count($resultCheck) > 0 ) {
                $result[$k1] = $resultCheck;
            }
            continue;
        }
        if ($v1 !== $a2[$k1]) {
            $result[$k1] = $v1;
        }
    }
    return $result;
}
/**
 *  Тестирование приложения
 */
class UnitTest
{
    private static function testDataProvider():array
    {
        $handlers = include __DIR__ . '/../config/request.handlers.php';
        $loggerFactory = static function():LoggerInterface {return new Logger();};
        return [
            [
                'testName'=>'Тестирование поиска получателя по id',
                'in' => [
                    'handlers' => $handlers,
                    'uri' => '/recipient?id_recipient=1',
                    'loggerFactory' =>'EfTech\ContactList\Infrastructure\Logger\Factory::create',
                    'appConfigFactory' => static function (){
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['loggerType'] = 'echoLogger';
                        return AppConfig::createFromArray($config);
                    }
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
                    'handlers' => $handlers,
                    'uri' => '/recipient?full_name=Осипов Геннадий Иванович',
                    'loggerFactory' => $loggerFactory,
                    'appConfigFactory' => static function (){
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToRecipients'] = __DIR__ . '/data/broken.recipient.json';
                        return AppConfig::createFromArray($config);
                    }
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
                    'handlers' => $handlers,
                    'uri' => '/recipient?id_recipient=1',
                    'loggerFactory' => $loggerFactory,
                    'appConfigFactory' => static function (){
                        return 'Ops!';
                    }
                ],
                'out' => [
                    'httpCode' => 500,
                    'result' => [
                        'status' => 'fail',
                        'message' => 'incorrect application config'
                    ]
                ]
            ],
            [
                'testName' => 'Тестирование ситуации с некорректным путем до файла получателями',
                'in' => [
                    'handlers' => $handlers,
                    'uri' =>  '/recipient?id_recipient=1',
                    'loggerFactory' => $loggerFactory,
                    'appConfigFactory' => static function (){
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToRecipients'] = __DIR__ . '/data/unknown.recipient.json';
                        return AppConfig::createFromArray($config);
    }
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
                    'handlers' => $handlers,
                    'uri' => '/customers?full_name=Калинин Пётр Александрович',
                    'loggerFactory' => $loggerFactory,
                    'appConfigFactory' => static function (){
                        $config = include __DIR__ . '/../config/dev/config.php';
                        $config['pathToCustomers'] = __DIR__ . '/data/broken.customers.json';
                        return AppConfig::createFromArray($config);
                    }
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

            $httpResponse = (new App(
                $testItem['in']['handlers'],
                $testItem['in']['loggerFactory'],
                $testItem['in']['appConfigFactory']
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