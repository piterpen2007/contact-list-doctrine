<?php

namespace EfTech\ContactList\Service\SearchContactsService;

class ColleaguesDto
{
    private int $id_recipient;
    private string $fullName;
    private string $birthday;
    private string $profession;
    private string $department;
    private string $position;
    private string $room_number;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param string $birthday
     * @param string $profession
     * @param string $department
     * @param string $position
     * @param string $room_number
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        string $department,
        string $position,
        string $room_number
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->department = $department;
        $this->position = $position;
        $this->room_number = $room_number;
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
}
