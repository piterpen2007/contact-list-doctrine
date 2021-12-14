<?php
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Logger\NullLogger\Logger;
use function EfTech\ContactList\Infrastructure\app;
require_once  __DIR__ . '/../src/Infrastructure/AppConfig.php';
require_once __DIR__ . '/../src/Infrastructure/app.function.php';
require_once __DIR__ . '/../src/Infrastructure/Logger/LoggerInterface.php';
require_once __DIR__ . '/../src/Infrastructure/Logger/NullLogger/Logger.php';
require_once __DIR__ . '/../src/Infrastructure/Logger/Factory.php';

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
                    $handlers,
                    '/recipient?id_recipient=1',
                    'EfTech\ContactList\Infrastructure\Logger\Factory::create',
                    static function (){
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
                    $handlers,
                    '/recipient?full_name=Осипов Геннадий Иванович',
                    $loggerFactory,
                    static function (){
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
                    $handlers,
                    '/recipient?id_recipient=1',
                    $loggerFactory,
                    static function (){
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
                    $handlers,
                    '/recipient?id_recipient=1',
                    $loggerFactory,
                    static function (){
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
                    $handlers,
                    '/customers?full_name=Калинин Пётр Александрович',
                    $loggerFactory,
                    static function (){
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
     */
    public static function runTest():void
    {
        foreach (static::testDataProvider() as $testItem) {
            echo "-----{$testItem['testName']}-----\n";
            //Arrange и Act
            $appResult = app(...$testItem['in']);

            //Assert
            if ($appResult['httpCode'] === $testItem['out']['httpCode']) {
                echo "    OK --- код ответа\n";
            } else {
                echo "    FAIL - код ответа. Ожидалось: {$testItem['out']['httpCode']}. Актуальное значение: {$appResult['httpCode']}\n";
            }
            $actualResult = json_decode(json_encode($appResult['result']), true);
            $unnecessaryElements = array_diff_assoc_recursive($actualResult, $testItem['out']['result']);
            $missingElements =  array_diff_assoc_recursive($testItem['out']['result'], $actualResult);

            $errMsg = '';

            if (count($unnecessaryElements) > 0) {
                $errMsg .= sprintf("         Есть лишние элементы %s\n", json_encode($unnecessaryElements,JSON_UNESCAPED_UNICODE));
            }
            if (count($missingElements) > 0) {
                $errMsg .= sprintf("         Есть лишние недостающие элементы %s\n", json_encode($missingElements,JSON_UNESCAPED_UNICODE));
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