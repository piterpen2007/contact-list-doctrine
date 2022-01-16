<?php

namespace EfTech\ContactList\Entity;

use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;

class ContactList
{

    /**
     * @var int id Получателя
     */
    private int $id_recipient;

    /**
     * @var int id записи
     */
    private int $id_entry;
    /**
     * @var bool наличие в черном списке
     */
    private bool $blackList;

    /**
     * @return int
     */
    public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /**
     * @param int $id_recipient
     * @return ContactList
     */
    public function setIdRecipient(int $id_recipient): ContactList
    {
        $this->id_recipient = $id_recipient;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdEntry(): int
    {
        return $this->id_entry;
    }

    /**
     * @param int $id_entry
     * @return ContactList
     */
    public function setIdEntry(int $id_entry): ContactList
    {
        $this->id_entry = $id_entry;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBlackList(): bool
    {
        return $this->blackList;
    }

    /**
     * @param bool $blackList
     * @return ContactList
     */
    public function setBlackList(bool $blackList): ContactList
    {
        $this->blackList = $blackList;
        return $this;
    }



    /**
     * @param int $id_recipient
     * @param int $id_entry
     * @param bool $blackList
     */
    public function __construct(int $id_recipient, int $id_entry, bool $blackList)
    {
        $this->id_recipient = $id_recipient;
        $this->id_entry = $id_entry;
        $this->blackList = $blackList;
    }
    /** Перенос контакта в черный список
     * @return $this
     */
    public function moveToBlacklist():self
    {
        if (true === $this->blackList) {
            throw new RuntimeException(
                "Контакт с id {$this->getIdRecipient()} уже находится в черном списке"
            );
        }
        $this->blackList = true;
        return $this;
    }



    /**
     * @param array $data
     * @return ContactList
     */
    public static function createFromArray(array $data):ContactList
    {
        $requiredFields = [
            'id_recipient',
            'id_entry',
            'blacklist'
        ];

        $missingFields = array_diff($requiredFields,array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new invalidDataStructureException($errMsg);
        }

        return new ContactList(
            $data['id_recipient'],
            $data['id_entry'],
            $data['blacklist']
        );
    }

}