<?php

namespace EfTech\ContactList\Service\SearchCustomersService;

class SearchCustomersCriteria
{
    private ?int $idRecipient;
    private ?string $fullName;
    private ?string $birthday;
    private ?string $profession;
    private ?string $contactNumber;
    private ?int $averageTransactionAmount;
    private ?string $discount;
    private ?string $timeToCall;

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @param int|null $idRecipient
     * @return SearchCustomersCriteria
     */
    public function setIdRecipient(?int $idRecipient): SearchCustomersCriteria
    {
        $this->idRecipient = $idRecipient;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return SearchCustomersCriteria
     */
    public function setFullName(?string $fullName): SearchCustomersCriteria
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * @param string|null $birthday
     * @return SearchCustomersCriteria
     */
    public function setBirthday(?string $birthday): SearchCustomersCriteria
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfession(): ?string
    {
        return $this->profession;
    }

    /**
     * @param string|null $profession
     * @return SearchCustomersCriteria
     */
    public function setProfession(?string $profession): SearchCustomersCriteria
    {
        $this->profession = $profession;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    /**
     * @param string|null $contactNumber
     * @return SearchCustomersCriteria
     */
    public function setContactNumber(?string $contactNumber): SearchCustomersCriteria
    {
        $this->contactNumber = $contactNumber;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAverageTransactionAmount(): ?int
    {
        return $this->averageTransactionAmount;
    }

    /**
     * @param int|null $averageTransactionAmount
     * @return SearchCustomersCriteria
     */
    public function setAverageTransactionAmount(?int $averageTransactionAmount): SearchCustomersCriteria
    {
        $this->averageTransactionAmount = $averageTransactionAmount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    /**
     * @param string|null $discount
     * @return SearchCustomersCriteria
     */
    public function setDiscount(?string $discount): SearchCustomersCriteria
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimeToCall(): ?string
    {
        return $this->timeToCall;
    }

    /**
     * @param string|null $timeToCall
     * @return SearchCustomersCriteria
     */
    public function setTimeToCall(?string $timeToCall): SearchCustomersCriteria
    {
        $this->timeToCall = $timeToCall;
        return $this;
    }
}
