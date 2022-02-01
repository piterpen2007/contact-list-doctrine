<?php

namespace EfTech\ContactListTest\ConsoleCommand;

use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\Infrastructure\Console\Output\BufferOutput;
use EfTech\ContactList\Infrastructure\DataLoader\JsonDataLoader;
use EfTech\ContactList\Infrastructure\Logger\Adapter\NullAdapter;
use EfTech\ContactList\Infrastructure\Logger\Logger;
use EfTech\ContactList\Repository\RecipientJsonFileRepository;
use EfTech\ContactList\Service\SearchRecipientsService;
use JsonException;
use PHPUnit\Framework\TestCase;

class FindRecipientTest extends TestCase
{
    /**
     * Поставщик данных
     *
     * @return array
     */
    public function findRecipientProvider(): array
    {
        return [
            'Тестирование ситуации, когда передан параметр id_recipient = 5'                         => [
                'in'  => [
                    'params' => ['id_recipient' => 5],
                ],
                'out' => [
                    'msg' => [
                        'id_recipient' => 5,
                        'full_name' => 'Шипенко Леонид Иосифович',
                        'birthday' => '07.02.1969',
                        'profession' => 'Слесарь',
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр f = Шипенко Леонид Иосифович' => [
                'in'  => [
                    'params' => ["full_name" => 'Шипенко Леонид Иосифович'],
                ],
                'out' => [
                    'msg' => [
                        'id_recipient' => 5,
                        'full_name' => 'Шипенко Леонид Иосифович',
                        'birthday' => '07.02.1969',
                        'profession' => 'Слесарь',
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр b = 15.06.1985 '                 => [
                'in'  => [
                    'params' => ["birthday" => '15.06.1985'],
                ],
                'out' => [
                    'msg' => [
                        'id_recipient' => 1,
                        'full_name' => 'Осипов Геннадий Иванович',
                        'birthday' => '15.06.1985',
                        'profession' => 'Системный администратор'
                    ],
                ],
            ],
            'Тестирование ситуации, когда передан короткий параметр p = Системный администратор'            => [
                'in'  => [
                    'params' => ["profession" => 'Системный администратор'],
                ],
                'out' => [
                    'msg' => [
                        'id_recipient' => 1,
                        'full_name' => 'Осипов Геннадий Иванович',
                        'birthday' => '15.06.1985',
                        'profession' => 'Системный администратор'
                    ],
                ],
            ],
        ];
    }

    /**
     *
     * @return void
     */
    public function testFindRecipientOpt(): void
    {
        $this->assertEquals(
            [
                'id_recipient:',
                'full_name:',
                'birthday:',
                'profession:',
            ],
            FindRecipients::getLongOption(),
            'некорректные длинные команды'
        );
        $this->assertEquals('i:f:b:p', FindRecipients::getShortOption(), 'некорректные короткие команды');
    }

    /**
     *
     * @dataProvider findRecipientProvider
     *
     * @param array $in
     * @param array $out
     *
     * @return void
     * @throws JsonException
     */
    public function testFindRecipient(array $in, array $out): void
    {
        $buffer = new BufferOutput();
        $findRecipient = new FindRecipients(
            $buffer,
            new SearchRecipientsService(
                new Logger(new NullAdapter()),
                new RecipientJsonFileRepository(
                    __DIR__ . '/../data/recipient.json',
                    new JsonDataLoader()
                )
            )
        );
        $findRecipient($in['params']);
        $this->assertEquals(
            $out['msg'],
            json_decode($buffer->getBuffer()[0], true, 512, JSON_THROW_ON_ERROR)[0],
            'некорректный ответ'
        );
    }
}
