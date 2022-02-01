<?php

namespace EfTech\ContactList\Service\SearchAddressService;

class SearchAddressCriteria
{
    private ?int $id_recipient = null;
    private ?int $id_address = null;
    private ?string $address = null;
    private ?string $status = null;

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->id_recipient;
    }

    /**
     * @param int|null $id_recipient
     * @return SearchAddressCriteria
     */
    public function setIdRecipient(?int $id_recipient): SearchAddressCriteria
    {
        $this->id_recipient = $id_recipient;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdAddress(): ?int
    {
        return $this->id_address;
    }

    /**
     * @param int|null $id_address
     * @return SearchAddressCriteria
     */
    public function setIdAddress(?int $id_address): SearchAddressCriteria
    {
        $this->id_address = $id_address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return SearchAddressCriteria
     */
    public function setAddress(?string $address): SearchAddressCriteria
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return SearchAddressCriteria
     */
    public function setStatus(?string $status): SearchAddressCriteria
    {
        $this->status = $status;
        return $this;
    }
}
