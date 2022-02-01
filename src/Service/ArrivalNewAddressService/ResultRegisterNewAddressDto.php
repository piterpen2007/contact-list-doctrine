<?php

namespace EfTech\ContactList\Service\ArrivalNewAddressService;

class ResultRegisterNewAddressDto
{
    private int $id_address;
    private int $id_recipient;
    private string $address;
    private string $status;

    /**
     * @param int $id_address
     * @param int $id_recipient
     * @param string $address
     * @param string $status
     */
    public function __construct(int $id_address, int $id_recipient, string $address, string $status)
    {
        $this->id_address = $id_address;
        $this->id_recipient = $id_recipient;
        $this->address = $address;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getIdAddress(): int
    {
        return $this->id_address;
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
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
