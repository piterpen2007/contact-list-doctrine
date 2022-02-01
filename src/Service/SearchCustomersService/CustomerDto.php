<?php

namespace EfTech\ContactList\Service\SearchCustomersService;

use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;

class CustomerDto
{
    private int $id_recipient;
    private string $fullName;
    private string $birthday;
    private string $profession;
    private string $contactNumber;
    private int $averageTransactionAmount;
    private string $discount;
    private string $timeToCall;
    public function __construct(
        int $id_recipient,
        string $fullName,
        string $birthday,
        string $profession,
        string $contactNumber,
        int $averageTransactionAmount,
        string $discount,
        string $timeToCall
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->contactNumber = $contactNumber;
        $this->averageTransactionAmount = $averageTransactionAmount;
        $this->discount = $discount;
        $this->timeToCall = $timeToCall;
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
}
