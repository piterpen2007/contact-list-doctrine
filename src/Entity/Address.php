<?php

namespace EfTech\ContactList\Entity;

use EfTech\ContactList\Exception\InvalidDataStructureException;

class Address
{
//    /**
//     * константа статуса
//     */
//    public const STATUS_HOME = 'home';
//    /**
//     * константа статуса
//     */
//    public const STATUS_WORK = 'work';
    /**
     * @var int id адреса
     */
    private int $id_address;
    /**
     * @var int id получателя
     */
    private int $id_recipient;
    /**
     * @var string адрес
     */
    private string $address;
    /** статус адреса (дом\работа)
     * @var string
     */
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
     * @param int $id_address
     * @return Address
     */
    public function setIdAddress(int $id_address): Address
    {
        $this->id_address = $id_address;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /**
     * @param int $id_recipient
     * @return Address
     */
    public function setIdRecipient(int $id_recipient): Address
    {
        $this->id_recipient = $id_recipient;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Address
     */
    public function setStatus(string $status): Address
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
