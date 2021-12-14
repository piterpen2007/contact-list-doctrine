<?php
namespace EfTech\ContactList\Infrastructure\Logger\NullLogger;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
require_once __DIR__ . '/../LoggerInterface.php';

/**
 *  Логгирует в никуда
 */
class Logger implements LoggerInterface
{
    /**
     * @inheritDoc
     *
     */
    public function log(string $msg): void
    {
        // TODO: Implement log() method.
    }

}