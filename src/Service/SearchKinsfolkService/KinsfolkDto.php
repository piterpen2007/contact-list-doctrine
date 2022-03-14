<?php

namespace EfTech\ContactList\Service\SearchKinsfolkService;

use EfTech\ContactList\ValueObject\Balance;

/**
 *  Структура информации о получателях
 */
class KinsfolkDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected string $birthday;
    protected string $profession;
    protected string $status;
    protected string $ringtone;
    protected string $hotkey;
    protected Balance $balance;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param string $birthday
     * @param string $profession
     * @param string $status
     * @param string $ringtone
     * @param string $hotkey
     * @param Balance $balance
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        string $status,
        string $ringtone,
        string $hotkey,
        Balance $balance
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->status = $status;
        $this->ringtone = $ringtone;
        $this->hotkey = $hotkey;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRingtone(): string
    {
        return $this->ringtone;
    }

    /**
     * @return string
     */
    public function getHotkey(): string
    {
        return $this->hotkey;
    }

    /**
     * @return Balance
     */
    public function getBalance(): Balance
    {
        return $this->balance;
    }
}
