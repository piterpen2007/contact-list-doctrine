<?php

namespace EfTech\ContactList\Service\SearchAddressService;

class AddressDto
{
    private ?array $id_recipient;
    private ?int $id_address;
    private ?string $address;
    private ?string $status;

    /**
     * @param array|null $id_recipient
     * @param int|null $id_address
     * @param string|null $address
     * @param string|null $status
     */
    public function __construct(?int $id_address, ?array $id_recipient, ?string $address, ?string $status)
    {
        $this->id_address = $id_address;
        $this->id_recipient = $id_recipient;
        $this->address = $address;
        $this->status = $status;
    }

    /**
     * @return array|null
     */
    public function getIdRecipient(): ?array
    {
        return $this->id_recipient;
    }

    /**
     * @return int|null
     */
    public function getIdAddress(): ?int
    {
        return $this->id_address;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
}
