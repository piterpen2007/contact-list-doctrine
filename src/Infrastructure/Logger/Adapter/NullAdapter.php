<?php

namespace EfTech\ContactList\Infrastructure\Logger\Adapter;

/**
 * Адпатер пишет логи в никуда
 */
class NullAdapter implements \EfTech\ContactList\Infrastructure\Logger\AdapterInterface
{

    /**
     * @inheritDoc
     */
    public function write(string $logLevel, string $msg): void
    {

    }
}