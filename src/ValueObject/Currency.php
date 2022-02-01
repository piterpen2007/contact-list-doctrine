<?php

namespace EfTech\ContactList\ValueObject;

use EfTech\ContactList\Exception\DomainException;

/**
 * Валюта
 */
final class Currency
{
    /** Код валюты
     * @var string
     */
    private string $code;

    /** Имя валюты
     * @var string
     */
    private string $name;

    /**
     * @param string $code Код валюты
     * @param string $name Имя валюты
     */
    public function __construct(string $code, string $name)
    {
        if (1 !== preg_match('/^[A-Z]{3}$/', $code)) {
            throw new DomainException('Некорректный формат кода валюты');
        }
        $this->code = $code;
        $this->name = $name;
    }

    /** Код валюты
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /** Имя валюты
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
