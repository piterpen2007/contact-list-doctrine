<?php
namespace EfTech\ContactList\Infrastructure\Logger\FileLogger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;

/**
 *  Логирует в файл
 */
class Logger implements LoggerInterface
{
    /**
     * @var string путь до файла где пишутся логи
     */
    private string $pathToFile;

    /**
     * @param string $pathToFile
     */
    public function __construct(string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
    }

    /**
     * @inheritDoc
     *
     */
    public function log(string $msg): void
    {
        file_put_contents($this->pathToFile, "$msg\n", FILE_APPEND);
    }
}