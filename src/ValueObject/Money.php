<?php

namespace EfTech\ContactList\ValueObject;

/**
 * Деньги
 */
final class Money
{
    /** Количество
     * @var int
     */
    private int $amount;
    /** Представление денег в формате плавующей точкой
     * @var float|null
     */
    private ?float $decimal = null;
    /** Валюта
     * @var Currency
     */
    private Currency $currency;

    /**
     * @param int $amount Количество
     * @param Currency $currency Валюта
     */
    public function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /** Количество
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /** Представление денег в формате плавующей точкой
     * @return float
     */
    public function getDecimal(): float
    {
        if (null === $this->decimal) {
            $this->decimal = $this->amount/100;
        }
        return $this->decimal;
    }

    /** Валюта
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }



}