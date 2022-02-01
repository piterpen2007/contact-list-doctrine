<?php

namespace EfTech\ContactListTest\ConsoleCommand;

use EfTech\ContactList\ConsoleCommand\HashStr;
use EfTech\ContactList\Infrastructure\Console\Output\BufferOutput;
use PHPUnit\Framework\TestCase;

class HashStrTest extends TestCase
{
    /**
     * Поставщик данных hashStr
     *
     * @return array
     */
    public function hashStrProvider(): array
    {
        return [
            'Тестирование ситуации, когда отсутствует ключ data'  => [
                'in'  => [
                    'params' => ['data2' => "admin"],
                ],
                'out' => [
                    'msg' => "Data for hashing is not specified",
                ],
            ],
            'Тестирование ситуации, когда параметр data является не строкой' => [
                'in'  => [
                    'params' => ["data" => []],
                ],
                'out' => [
                    'msg' => "Hash data is not in the correct format",
                ],
            ],
        ];
    }

    /**
     * Тестирование методов класса HashStr
     *
     * @return void
     */
    public function testHashStrMethod(): void
    {
        $buffer = new BufferOutput();
        $hashStr = new HashStr($buffer);
        $this->assertEquals(['data:'], $hashStr::getLongOption(), 'некорректные длинные опции');
        $this->assertEquals('', $hashStr::getShortOption(), 'некорректные короткие опции');
    }

    /**
     * Тестирование HashStr
     *
     * @dataProvider hashStrProvider
     *
     * @param array $in
     * @param array $out
     *
     * @return void
     */
    public function testHashStr(array $in, array $out): void
    {
        $buffer = new BufferOutput();
        $hashStr = new HashStr($buffer);
        $hashStr($in['params']);
        $this->assertEquals($out['msg'], $buffer->getBuffer()[0], 'FAIL: Некорректный ответ');
    }
}
