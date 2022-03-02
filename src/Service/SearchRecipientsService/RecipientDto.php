<?php

namespace EfTech\ContactList\Service\SearchRecipientsService;

use EfTech\ContactList\ValueObject\Balance;

/**
 *  Структура информации о получателях
 */
class RecipientDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected string $birthday;
    protected string $profession;
    protected Balance $balance;


    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param string $birthday
     * @param string $profession
     * @param Balance $balance
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        Balance $balance
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getProfession(): string
    {
        return $this->profession;
    }

    /**
     * @return Balance
     */
    public function getBalance(): Balance
    {
        return $this->balance;
    }

}
