<?php

namespace EfTech\ContactListTest\ConsoleCommand;

use EfTech\ContactList\ConsoleCommand\FindContacts;
use EfTech\ContactList\Infrastructure\Console\Output\BufferOutput;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Repository\ContactJsonRepository;
use EfTech\ContactList\Service\SearchContactsService;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FindContactTest extends TestCase
{
    /**
     * Поставщик данных
     *
     * @return array
     */
    public function findContactProvider(): array
    {
        return [
            'Тестирование ситуации, когда передан параметр category = colleagues'                         => [
                'in'  => [
                    'params' => ['category' => 'colleagues'],
                ],
                'out' => [
                    'msg' => [
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
                ],
            ]
        ];
    }

    /**
     *
     * @return void
     */
    public function testFindContactOpt(): void
    {
        $this->assertEquals(
            [
                'category:'
            ],
            FindContacts::getLongOption(),
            'некорректные длинные команды'
        );
        $this->assertEquals('c:', FindContacts::getShortOption(), 'некорректные короткие команды');
    }

    /**
     *
     * @dataProvider findContactProvider
     *
     * @param array $in
     * @param array $out
     *
     * @return void
     * @throws JsonException
     */
    public function testFindContact(array $in, array $out): void
    {
        $buffer = new BufferOutput();
        $findContacts = new FindContacts(
            $buffer,
            new SearchContactsService(
                new ContactJsonRepository(
                    __DIR__ . '/../data/recipient.json',
                    __DIR__ . '/../data/customers.json',
                    __DIR__ . '/../data/kinsfolk.json',
                    __DIR__ . '/../data/colleagues.json',
                    new JsonDataLoader()
                ),
                new NullLogger()
            )
        );
        $findContacts($in['params']);
        $this->assertEquals(
            $out['msg'][0],
            json_decode($buffer->getBuffer()[0], true, 512, JSON_THROW_ON_ERROR)[0],
            'некорректный ответ'
        );

    }
}
