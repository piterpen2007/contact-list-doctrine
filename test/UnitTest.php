<?php

namespace EfTech\ContactListTest;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Infrastructure\DI\Container;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\AdapterInterface;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Infrastructure\View\NullRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *  Тестирование приложения
 */
class UnitTest extends TestCase
{
    public static function bugFactory(array $config): string
    {
        return 'Ops!';
    }
    /**
     * Создаёт DI контайнер симфони
     * @throws Exception
     */
    private static function createDiContainer(): ContainerBuilder
    {
        $containerBuilder = SymfonyDiContainerInit::createContainerBuilder(
            __DIR__ . '/../config/dev/di.xml',
            [
                'kernel.project_dir' => __DIR__ . '/../'
            ]
        );
        $containerBuilder->getDefinition(AdapterInterface::class)
            ->setClass(NullAdapter::class)
            ->addArgument([]);
        $containerBuilder->getDefinition(RenderInterface::class)
            ->setClass(NullRender::class)
            ->addArgument([]);
        return $containerBuilder;
    }

    /** Поставщик данных для тестирования приложения
     * @return array
     * @throws Exception
     */
    public static function dataProvider(): array
    {
        return [
            'Тестирование поиска получателя по id' => [
                'in' => [
                    'uri' => '/recipients?id_recipient=1',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
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
            'Тестирование поиска получателя по full_name' => [
                'in' => [
                    'uri' => '/recipients?full_name=Дамир Авто',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            'id_recipient' => 3,
                            'full_name' => 'Дамир Авто',
                            'birthday' => '01.12.1990',
                            'profession' => 'Автомеханик'
                        ],
                    ],
                ]
            ],
            'Тестирование поиска получателя по profession' => [
                'in' => [
                    'uri' => '/recipients?profession=Автомеханик',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            'id_recipient' => 3,
                            'full_name' => 'Дамир Авто',
                            'birthday' => '01.12.1990',
                            'profession' => 'Автомеханик'
                        ],
                    ],
                ]
            ],
            'Тестирование поиска клиентов по id' => [
                'in' => [
                    'uri' => '/customers?id_recipient=7',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            "id_recipient" => 7,
                            "full_name" => "Калинин Пётр Александрович",
                            "birthday" => "04.06.1983",
                            "profession" => "Фитнес тренер",
                            "contract_number" => "5684",
                            "average_transaction_amount" => 2500,
                            "discount" => "5%",
                            "time_to_call" => "С 9:00 до 13:00 в будни",
                        ],
                    ],
                ]
            ],
            'Тестирование поиска клиентов по time to call' => [
                'in' => [
                    'uri' => '/customers?time_to_call=С 9:00 до 13:00 в будни',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            "id_recipient" => 7,
                            "full_name" => "Калинин Пётр Александрович",
                            "birthday" => "04.06.1983",
                            "profession" => "Фитнес тренер",
                            "contract_number" => "5684",
                            "average_transaction_amount" => 2500,
                            "discount" => "5%",
                            "time_to_call" => "С 9:00 до 13:00 в будни",
                        ],
                    ],
                ]
            ],
            'Тестирование поиска контактов по категории colleagues' => [
                'in' => [
                    'uri' => '/contact?category=colleagues',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            "id_recipient" => 10,
                            "full_name" => "Шатов Александр Иванович",
                            "birthday" => "02.12.1971",
                            "profession" => "",
                            "department" => "Дирекция",
                            "position" => "Директор",
                            "room_number" => "405"
                        ],
                        [
                            "id_recipient" => 11,
                            "full_name" => "Наташа",
                            "birthday" => "10.05.1984",
                            "profession" => "",
                            "department" => "Дирекция",
                            "position" => "Секретарь",
                            "room_number" => "404"
                        ]
                    ],
                ]
            ],
            'Тестирование поиска контактов по категории kinsfolk' => [
                'in' => [
                    'uri' => '/contact?category=kinsfolk',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
                ],
                'out' => [
                    'httpCode' => 200,
                    'result' => [
                        [
                            "id_recipient" => 6,
                            "full_name" => "Дед",
                            "birthday" => "04.06.1945",
                            "profession" => "Столяр",
                            "status" => "Дед",
                            "ringtone" => "Bells",
                            "hotkey" => "1"
                        ]
                    ],
                ]
            ],
            'Тестирование ситуации когда данные о получателях не кореектны. Нет поля birthday' => [
                'in' => [
                    'uri' => '/recipients?full_name=Осипов Геннадий Иванович',
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $appConfigParams = $c->getParameter('app.configs');
                        $appConfigParams['pathToRecipients'] = __DIR__ . '/data/broken.recipient.json';
                        $c->setParameter('app.configs', $appConfigParams);
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
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
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $c->getDefinition(AppConfig::class)->setFactory([UnitTest::class, 'bugFactory']);
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
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
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $appConfigParams = $c->getParameter('app.configs');
                        $appConfigParams['pathToRecipients'] = __DIR__ . '/data/unknown.recipients.json';
                        $c->setParameter('app.configs', $appConfigParams);
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
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
                    'diContainer' => (static function (ContainerBuilder $c): ContainerBuilder {
                        $appConfigParams = $c->getParameter('app.configs');
                        $appConfigParams['pathToCustomers'] = __DIR__ . '/data/broken.customers.json';
                        $c->setParameter('app.configs', $appConfigParams);
                        $c->compile();
                        return $c;
                    })(self::createDiContainer())
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
        $diContainer = $in['diContainer'];
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
            static function () use ($diContainer): ContainerInterface {
                return $diContainer;
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
