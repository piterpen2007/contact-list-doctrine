<?php

namespace EfTech\ContactList\Entity;

use EfTech\ContactList\Entity\Address\Status;
use EfTech\ContactList\Exception\InvalidDataStructureException;

class Address
{
    /**
     * @var int id адреса
     */
    private int $idAddress;
    /**
     * @var int[] id получателей
     */
    private array $idRecipient;
    /**
     * @var string адрес
     */
    private string $address;
    /** статус адреса (дом\работа)
     *
     * @var Status
     */
    private Status $status;

    /**
     * @param int $idAddress
     * @param array $idRecipient
     * @param string $address
     * @param Status $status
     */
    public function __construct(int $idAddress, array $idRecipient, string $address, Status $status)
    {
        $this->idAddress = $idAddress;
        $this->idRecipient = $idRecipient;
        $this->address = $address;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getIdAddress(): int
    {
        return $this->idAddress;
    }

    /**
     * @param int $idAddress
     * @return Address
     */
    public function setIdAddress(int $idAddress): Address
    {
        $this->idAddress = $idAddress;
        return $this;
    }

    /**
     * @return array
     */
    public function getIdRecipient(): array
    {
        return $this->idRecipient;
    }

    /**
     * @param array $idRecipient
     * @return Address
     */
    public function setIdRecipient(array $idRecipient): Address
    {
        $this->idRecipient = $idRecipient;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Address
     */
    public function setAddress(string $address): Address
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     * @return Address
     */
    public function setStatus(Status $status): Address
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param array $data
     * @return Address
     */
    public static function createFromArray(array $data): Address
    {
        $requiredFields = [
            'id_address',
            'id_recipient',
            'address',
            'status'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new InvalidDataStructureException($errMsg);
        }

        return new Address(
            $data['id_address'],
            $data['id_recipient'],
            $data['address'],
            $data['status']
        );
    }
}
