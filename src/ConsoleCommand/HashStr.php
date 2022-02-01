<?php

namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;

class HashStr implements CommandInterface
{
    private OutputInterface $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }


    /**
     * @inheritDoc
     */
    public static function getShortOption(): string
    {
        return '';
    }

    /**
     * @inheritDocda
     */
    public static function getLongOption(): array
    {
        return [
            'data:'
        ];
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $params): void
    {
        if (false === array_key_exists('data', $params)) {
            $msg = 'Data for hashing is not specified';
        } elseif (false === is_string($params['data'])) {
            $msg = 'Hash data is not in the correct format';
        } else {
            $msg = password_hash($params['data'], PASSWORD_DEFAULT);
        }
        $this->output->print($msg);
    }
}
