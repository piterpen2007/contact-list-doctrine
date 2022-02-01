<?php

namespace EfTech\ContactListTest;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Infrastructure\View\NullRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 *  Тестирование приложения
 */
class UnitTest extends TestCase
{
    /** Поставщик данных для тестирования приложения
     * @return array
     */
    public static function dataProvider(): array
    {
        $diConfig = require __DIR__ . '/../config/dev/di.php';
        $diConfig['services'][\EfTech\ContactList\Infrastructure\Logger\AdapterInterface::class] = [
            'class' => \EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter::class
        ];
        $diConfig['services'][RenderInterface::class] = [
            'class' => NullRender::class
        ];
        return [
            'Тестирование поиска получателя по id' => [
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
            'Тестирование ситуации когда данные о получателях не кореектны. Нет поля birthday' => [
                'in' => [
                    'uri' => '/recipient?full_name=Осипов Геннадий Иванович',
                    'diConfig' => (static function ($diConfig) {
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
            'Тестирование ситуации с некорректным  данными конфига приложения' => [
                'in' => [
                    'uri' => '/recipient?id_recipient=1',
                    'diConfig' => (static function ($diConfig) {
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
            'Тестирование ситуации с некорректным путем до файла получателями' => [
                'in' => [
                    'uri' =>  '/recipient?id_recipient=1',
                    'diConfig' => (static function ($diConfig) {
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
            'Тестирование ситуации когда данные о клиентах некорректны. Нет поля id_recipient' => [
                'in' => [
                    'uri' => '/customers?full_name=Калинин Пётр Александрович',
                    'diConfig' => (static function ($diConfig) {
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
            ]
        ];
    }

    /** Запускает тест
     * @param array $in - входные данные
     * @param array $out
     * @dataProvider  dataProvider
     * @throws JsonException
     */
    public function testApp(array $in, array $out): void
    {
        $httpRequest = new ServerRequest(
            'GET',
            '1.1',
            $in['uri'],
            Uri::createFromString($in['uri']),
            ['Content-Type' => 'application/json'],
            null
        );
        //Arrange и Act
        $diConfig = $in['diConfig'];
        $httpResponse = (new App(
            static function (ContainerInterface $di): RouterInterface {
                return $di->get(RouterInterface::class);
            },
            static function (ContainerInterface $di): LoggerInterface {
                return $di->get(LoggerInterface::class);
            },
            static function (ContainerInterface $di): AppConfig {
                return $di->get(AppConfig::class);
            },
            static function (ContainerInterface $di): RenderInterface {
                return $di->get(RenderInterface::class);
            },
            static function () use ($diConfig): ContainerInterface {
                return Container::createFromArray($diConfig);
            }
        ))->dispath($httpRequest);
        // Assert
        $this->assertEquals($out['httpCode'], $httpResponse->getStatusCode(), 'код ответа');
        $this->assertEquals(
            $out['result'],
            $actualResult =  json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR),
            'структура ответа'
        );
    }
}
