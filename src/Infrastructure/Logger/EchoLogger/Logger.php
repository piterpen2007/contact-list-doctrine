<?php
namespace EfTech\ContactList\Infrastructure\Logger\EchoLogger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
require_once __DIR__ . '/../LoggerInterface.php';

/**
 *  Логирует в консоль с помощью echo
 */
class Logger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function log(string $msg): void
    {
        echo "$msg\n";
    }
}