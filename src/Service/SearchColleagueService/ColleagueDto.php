<?php

namespace EfTech\ContactList\Service\SearchColleagueService;

use DateTimeImmutable;
use EfTech\ContactList\ValueObject\Balance;

/**
 *  Структура информации о получателях
 */
class ColleagueDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected DateTimeImmutable $birthday;
    protected string $profession;
    protected string $department;
    protected string $position;
    protected string $room_number;
    protected array $emails;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param string $department
     * @param string $position
     * @param string $room_number
     * @param array $emails
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        DateTimeImmutable $birthday,
        string $profession,
        string $department,
        string $position,
        string $room_number,
        array $emails
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->department = $department;
        $this->position = $position;
        $this->room_number = $room_number;
        $this->emails = $emails;
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
     * @return DateTimeImmutable
     */
    public function getBirthday(): DateTimeImmutable
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
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }


}
