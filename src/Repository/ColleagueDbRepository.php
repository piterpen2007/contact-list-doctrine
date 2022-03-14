<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ColleagueRepositoryInterface;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;

class ColleagueDbRepository implements ColleagueRepositoryInterface
{
    /**
     * Соединение с БД
     *
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;

    /**
     * Критерии поиска
     */
    private const ALLOWED_CRITERIA = [
        'id_recipient' => 'r.id_recipient',
        'full_name' => 'r.full_name',
        'birthday' => 'r.birthday',
        'profession' => 'r.profession',
        'department' => 'c.department',
        'position' => 'c.position',
        'room_number' => 'c.room_number'
    ];
    /**
     * @param ConnectionInterface $connection - Соединение с БД
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Валидация критериев поиска
     *
     * @param array $criteria - Входные критерии
     *
     * @return void
     */
    private function validateCriteria(array $criteria): void
    {
        $invalidCriteria = array_diff(array_keys($criteria), array_keys(self::ALLOWED_CRITERIA));

        if (0 < count($invalidCriteria)) {
            $errMsg = 'Неподдерживаемые критерии поиска коллег ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }


    public function findBy(array $searchCriteria): array
    {
        $this->validateCriteria($searchCriteria);

        $whereParts = [];
        $whereParams = [];

        foreach ($searchCriteria as $criteriaName => $criteriaValue) {
            $criteriaToSqlParts = self::ALLOWED_CRITERIA;

            $whereParts[] = "{$criteriaToSqlParts[$criteriaName]}=:$criteriaName";
            $whereParams[$criteriaName] = $criteriaValue;
        }

        $sql = <<<EOF
SELECT
       r.id_recipient as id_recipient, 
       r.full_name as full_name,
       r.birthday as birthday, 
       r.profession as profession, 
       c.department as department, 
       c.position as position, 
       c.room_number as room_number,  
       r.amount as amount, 
       r.currency as currency
FROM recipients as r
JOIN colleagues c on r.id_recipient = c.id_recipient
WHERE r.type = 'colleague'
EOF;

        if (0 < count($whereParts)) {
            $sql .= ' and ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $colleagueData = $statement->fetchAll();

        $foundColleagues = [];

        foreach ($colleagueData as $colleagueItem) {
            $balance['currency'] = $colleagueItem['currency'];
            $balance['amount'] = $colleagueItem['amount'];
            $colleagueItem['balance'] = $this->createBalanceData($balance);
            unset($colleagueItem['currency'], $colleagueItem['amount']);
            $colleagueObj = Colleague::createFromArray($colleagueItem);
            $foundColleagues[$colleagueObj->getIdRecipient()] = $colleagueObj;
        }
        return $foundColleagues;
    }


    private function createBalanceData(array $balances): Balance
    {
        if (false === is_array($balances)) {
            throw new InvalidDataStructureException('Данные о балансе имеют невалидный формат');
        }
        if (false === array_key_exists('amount', $balances)) {
            throw new InvalidDataStructureException('Отсутствуют данные о деньгах на балансе');
        }
        if (false === is_int($balances['amount'])) {
            throw new InvalidDataStructureException('Данные о самом балансе имеют неверный формат');
        }
        if (false === array_key_exists('currency', $balances)) {
            throw new InvalidDataStructureException('Отсутствуют данные о валюте');
        }
        if (false === is_string($balances['currency'])) {
            throw new InvalidDataStructureException('Данные о валюте имеют не верный формат');
        }
        $currencyName = 'RUB' === $balances['currency'] ? 'рубль' : 'неизвестно';
        return new Balance(
            new Money(
                $balances['amount'],
                new Currency($balances['currency'], $currencyName)
            )
        );
    }
}
