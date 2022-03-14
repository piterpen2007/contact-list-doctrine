<?php

namespace EfTech\ContactList\Service\SearchColleagueService;

use EfTech\ContactList\ValueObject\Balance;

/**
 *  Структура информации о получателях
 */
class ColleagueDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected string $birthday;
    protected string $profession;
    protected string $department;
    protected string $position;
    protected string $room_number;
    protected Balance $balance;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param string $birthday
     * @param string $profession
     * @param string $department
     * @param string $position
     * @param string $room_number
     * @param Balance $balance
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        string $department,
        string $position,
        string $room_number,
        Balance $balance
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->department = $department;
        $this->position = $position;
        $this->room_number = $room_number;
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
    public function getDepartment(): string
    {
        return $this->department;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getRoomNumber(): string
    {
        return $this->room_number;
    }

    /**
     * @return Balance
     */
    public function getBalance(): Balance
    {
        return $this->balance;
    }


}
