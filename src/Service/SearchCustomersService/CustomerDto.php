<?php

namespace EfTech\ContactList\Service\SearchCustomersService;

use DateTimeImmutable;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;

class CustomerDto
{
    private int $id_recipient;
    private string $fullName;
    private DateTimeImmutable $birthday;
    private array $emails;
    private string $profession;
    private string $contactNumber;
    private int $averageTransactionAmount;
    private string $discount;
    private string $timeToCall;
    public function __construct(
        int $id_recipient,
        string $fullName,
        DateTimeImmutable $birthday,
        string $profession,
        string $contactNumber,
        int $averageTransactionAmount,
        string $discount,
        string $timeToCall,
        array $emails
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->contactNumber = $contactNumber;
        $this->averageTransactionAmount = $averageTransactionAmount;
        $this->discount = $discount;
        $this->timeToCall = $timeToCall;
        $this->emails = $emails;
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
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
