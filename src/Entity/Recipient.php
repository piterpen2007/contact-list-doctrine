<?php

namespace EfTech\ContactList\Entity;

use EfTech\ContactList\Exception;
use EfTech\ContactList\Exception\DomainException;
use EfTech\ContactList\ValueObject\Balance;
use JsonSerializable;

class Recipient
{
    /** баланс контакта
     * @var Balance
     */
    private Balance $balances;
    /**
     * @var int id Получателя
     */
    private int $id_recipient;
    /**
     * @var string Полное имя получателя
     */
    private string $full_name;
    /**
     * @var string Дата рождения получателя
     */
    private string $birthday;
    /**
     * @var string Профессия получателя
     */
    private string $profession;

    /** Конструктор класса
     * @param int $id_recipient
     * @param string $full_name
     * @param string $birthday
     * @param string $profession
     * @param Balance $balances
     */
    public function __construct(
        int $id_recipient,
        string $full_name,
        string $birthday,
        string $profession,
        Balance $balances
    ) {
        $this->id_recipient = $id_recipient;
        $this->full_name = $full_name;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->balances = $balances;
    }

    /** Возвращает данные о балансе
     * @return Balance
     */
    public function getBalance(): Balance
    {
        return $this->balances;
    }

    /**
     * @return int Возвращает id получателя
     */
    final public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /** Устанавливает id получателя
     * @param int $id_recipient
     * @return Recipient
     */
    public function setIdRecipient(int $id_recipient): Recipient
    {
        $this->id_recipient = $id_recipient;
        return $this;
    }

    /** Возвращает полное имя получателя
     * @return string
     */
    final public function getFullName(): string
    {
        return $this->full_name;
    }

    /** Устанавливает полное имя получателя
     * @param string $full_name
     * @return Recipient
     */
    public function setFullName(string $full_name): Recipient
    {
        $this->full_name = $full_name;
        return $this;
    }

    /** Возвращает дату рождения получателя
     * @return string
     */
    final public function getBirthday(): string
    {
        return $this->birthday;
    }

    /** Устанавливает дату рождения получателя
     * @param string $birthday
     * @return Recipient
     */
    public function setBirthday(string $birthday): Recipient
    {
        $this->birthday = $birthday;
        return $this;
    }

    /** Возвращает профессию получателя
     * @return string
     */
    final public function getProfession(): string
    {
        return $this->profession;
    }

    /** Устанавливает профессию получателя
     * @param string $profession
     * @return Recipient
     */
    public function setProfession(string $profession): Recipient
    {
        $this->profession = $profession;
        return $this;
    }

    /**
     * @param array $data
     * @return Recipient
     */
    public static function createFromArray(array $data): Recipient
    {
        $requiredFields = [
            'id_recipient',
            'full_name',
            'birthday',
            'profession',
            'balance'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new Exception\InvalidDataStructureException($errMsg);
        }

        return new Recipient(
            $data['id_recipient'],
            $data['full_name'],
            $data['birthday'],
            $data['profession'],
            $data['balance']
        );
    }
}
