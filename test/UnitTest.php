<?php

namespace EfTech\ContactListTest;

use EfTech\ContactList\Config\AppConfig;
use EfTech\ContactList\Config\ContainerExtensions;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use EfTech\ContactList\Infrastructure\HttpApplication\App;
use EfTech\ContactList\Infrastructure\Router\RouterInterface;
use EfTech\ContactList\Infrastructure\View\NullRender;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use Exception;
use JsonException;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

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
            new SymfonyDiContainerInit\ContainerParams(
                __DIR__ . '/../config/dev/di.xml',
                [
                    'kernel.project_dir' => __DIR__ . '/../'
                ],
                ContainerExtensions::httpAppContainerExtension()
            )
        );
        $containerBuilder->removeAlias(LoggerInterface::class);
        $containerBuilder->setDefinition(NullLogger::class, new Definition());
        $containerBuilder->setAlias(LoggerInterface::class, NullLogger::class)->setPublic(true);


        $containerBuilder->getDefinition(RenderInterface::class)
            ->setClass(NullRender::class)
            ->setArguments([]);
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
                            'profession' => 'Системный администратор',
                            "balance" => [
                                "amount" => 2220,
                                "currency" => "рубль"
                            ]
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
                            'profession' => 'Автомеханик',
                            "balance" => [
                                "amount" => 4566,
                                "currency" => "рубль"
                            ]
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
                            'profession' => 'Автомеханик',
                            "balance" => [
                                "amount" => 4566,
                                "currency" => "рубль"
                            ]
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
            new Uri($in['uri']),
            ['Content-Type' => 'application/json'],
        );
        $queryParams = [];
        parse_str($httpRequest->getUri()->getQuery(), $queryParams);
        $httpRequest = $httpRequest->withQueryParams($queryParams);

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
