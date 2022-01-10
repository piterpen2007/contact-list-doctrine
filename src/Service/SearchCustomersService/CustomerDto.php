<?php
namespace EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;

class CustomerDto extends RecipientDto
{
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
    )
    {
        parent::__construct($id_recipient, $fullName, $birthday, $profession);
        $this->contactNumber = $contactNumber;
        $this->averageTransactionAmount = $averageTransactionAmount;
        $this->discount = $discount;
        $this->timeToCall = $timeToCall;
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