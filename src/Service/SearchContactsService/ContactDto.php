<?php

namespace EfTech\ContactList\Service\SearchContactsService;

class ContactDto
{
    private int $id_recipient;
    private string $fullName;
    private string $birthday;
    private string $profession;
    private string $status;
    private string $ringtone;
    private string $hotkey;
    private string $contactNumber;
    private int $averageTransactionAmount;
    private string $discount;
    private string $timeToCall;
    private string $department;
    private string $position;
    private string $room_number;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param string $birthday
     * @param string $profession
     * @param string $status
     * @param string $ringtone
     * @param string $hotkey
     * @param string $contactNumber
     * @param int $averageTransactionAmount
     * @param string $discount
     * @param string $timeToCall
     * @param string $department
     * @param string $position
     * @param string $room_number
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        string $status,
        string $ringtone,
        string $hotkey,
        string $contactNumber,
        int $averageTransactionAmount,
        string $discount,
        string $timeToCall,
        string $department,
        string $position,
        string $room_number
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->status = $status;
        $this->ringtone = $ringtone;
        $this->hotkey = $hotkey;
        $this->contactNumber = $contactNumber;
        $this->averageTransactionAmount = $averageTransactionAmount;
        $this->discount = $discount;
        $this->timeToCall = $timeToCall;
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
     * @return string
     */
    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }

    /**
     * @return int
     */
    public function getAverageTransactionAmount(): int
    {
        return $this->averageTransactionAmount;
    }

    /**
     * @return string
     */
    public function getDiscount(): string
    {
        return $this->discount;
    }

    /**
     * @return string
     */
    public function getTimeToCall(): string
    {
        return $this->timeToCall;
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