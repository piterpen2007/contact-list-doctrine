<?php

namespace EfTech\ContactList\Infrastructure\Logger\Adapter;

class EchoAdapter implements \EfTech\ContactList\Infrastructure\Logger\AdapterInterface
{

    /**
     * @inheritDoc
     */
    public function write(string $logLevel, string $msg): void
    {
        echo "$msg\n";
    }
}