<?php

namespace EfTech\ContactList\Service\MoveToBlacklistService;

class MoveToBlacklistDto
{
    /**
     * @var bool наличие в черном списке
     */
    private bool $blackList;

    /**
     * @param bool $blackList
     */
    public function __construct(bool $blackList)
    {

        $this->blackList = $blackList;
    }
    /**
     * @return bool
     */
    public function isBlackList(): bool
    {
        return $this->blackList;
    }
}
