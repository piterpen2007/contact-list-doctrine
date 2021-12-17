<?php
namespace EfTech\ContactList\Infrastructure\Logger\EchoLogger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;

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