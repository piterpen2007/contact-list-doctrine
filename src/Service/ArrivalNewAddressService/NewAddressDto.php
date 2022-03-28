<?php

namespace EfTech\ContactList\Service\ArrivalNewAddressService;

use EfTech\ContactList\Entity\Address\Status;

class NewAddressDto
{
    private array $id_recipient;
    private string $address;
    private string $status;
/**
     * @param array $id_recipient
     * @param string $address
     * @param string $status
     */
    public function __construct(array $id_recipient, string $address, string $status)
    {
        $this->id_recipient = $id_recipient;
        $this->address = $address;
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getIdRecipient(): array
    {
        return $this->id_recipient;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return Status
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
