<?php

namespace EfTech\ContactList\ValueObject;

/**
 * Баланс
 */
final class Balance
{
    /** Деньги
     * @var Money
     */
    private Money $money;

    /**
     * @param Money $money Деньги
     */
    public function __construct(Money $money)
    {
        $this->money = $money;
    }


    /** Деньги
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }
}
