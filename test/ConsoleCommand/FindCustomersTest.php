<?php

namespace EfTech\ContactListTest\ConsoleCommand;

use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\Infrastructure\Console\Output\BufferOutput;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Repository\CustomerJsonFileRepository;
use EfTech\ContactList\Service\SearchCustomersService;
use JsonException;
use PHPUnit\Framework\TestCase;

class FindCustomersTest extends TestCase
{
    /**
     * Поставщик данных
     *
     * @return array
     */
    public function findCustomersProvider(): array
    {
        return [
            'Тестирование ситуации, когда передан параметр id_recipient = 8'                         => [
                'in'  => [
                    'params' => ['id_recipient' => 8],
                ],
                'out' => [
                    'msg' => [
                        "id_recipient" => 8,
                        "full_name" => "Васин Роман Александрович",
                        "birthday" => "04.01.1977",
                        "profession" => "Фитнес тренер",
                        "contract_number" => "5683",
                        "average_transaction_amount" => 9500,
                        "discount" => "10%",
                        "time_to_call" => "С 12:00 до 16:00 в будни"
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр full_name = Васин Роман Александрович' => [
                'in'  => [
                    'params' => ["full_name" => 'Васин Роман Александрович'],
                ],
                'out' => [
                    'msg' => [
                        "id_recipient" => 8,
                        "full_name" => "Васин Роман Александрович",
                        "birthday" => "04.01.1977",
                        "profession" => "Фитнес тренер",
                        "contract_number" => "5683",
                        "average_transaction_amount" => 9500,
                        "discount" => "10%",
                        "time_to_call" => "С 12:00 до 16:00 в будни"
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр contract_number = 5683 '                 => [
                'in'  => [
                    'params' => ["contract_number" => '5683'],
                ],
                'out' => [
                    'msg' => [
                        "id_recipient" => 8,
                        "full_name" => "Васин Роман Александрович",
                        "birthday" => "04.01.1977",
                        "profession" => "Фитнес тренер",
                        "contract_number" => "5683",
                        "average_transaction_amount" => 9500,
                        "discount" => "10%",
                        "time_to_call" => "С 12:00 до 16:00 в будни"
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр average_transaction_amount = 9500'            => [
                'in'  => [
                    'params' => ["time_to_call" => 'С 12:00 до 16:00 в будни'],
                ],
                'out' => [
                    'msg' => [
                        "id_recipient" => 8,
                        "full_name" => "Васин Роман Александрович",
                        "birthday" => "04.01.1977",
                        "profession" => "Фитнес тренер",
                        "contract_number" => "5683",
                        "average_transaction_amount" => 9500,
                        "discount" => "10%",
                        "time_to_call" => "С 12:00 до 16:00 в будни"
                    ],
                ],
            ],
        ];
    }

    /**
     *
     * @return void
     */
    public function testFindCustomersOpt(): void
    {
        $this->assertEquals(
            [
                'id_recipient:',
                'full_name:',
                'birthday:',
                'profession:',
                'contract_number:',
                'average_transaction_amount:',
                'discount:',
                'time_to_call'
            ],
            FindCustomers::getLongOption(),
            'некорректные длинные команды'
        );
        $this->assertEquals('i:f:b:p:c:a:d:t', FindCustomers::getShortOption(), 'некорректные короткие команды');
    }

    /**
     *
     * @dataProvider findCustomersProvider
     *
     * @param array $in
     * @param array $out
     *
     * @return void
     * @throws JsonException
     */
    public function testFindCustomers(array $in, array $out): void
    {
        $buffer = new BufferOutput();
        $findCustomers = new FindCustomers(
            $buffer,
            new SearchCustomersService(
                new CustomerJsonFileRepository(
                    __DIR__ . '/../data/customers.json',
                    new JsonDataLoader()
                ),
                new Logger(new NullAdapter())
            )
        );
        $findCustomers($in['params']);
        $this->assertEquals(
            $out['msg'],
            json_decode($buffer->getBuffer()[0], true, 512, JSON_THROW_ON_ERROR)[0],
            'некорректный ответ'
        );
    }
}
