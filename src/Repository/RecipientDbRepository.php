<?php

namespace EfTech\ContactList\Repository;

use DateTimeImmutable;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;

class RecipientDbRepository implements RecipientRepositoryInterface
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
        'id_recipient',
        'full_name',
        'birthday',
        'profession'
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
        $invalidCriteria = array_diff(array_keys($criteria), self::ALLOWED_CRITERIA);

        if (0 < count($invalidCriteria)) {
            $errMsg = 'Неподдерживаемые критерии поиска получателей ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }

    public function findBy(array $searchCriteria): array
    {
        $this->validateCriteria($searchCriteria);

        $whereParts = [];
        $whereParams = [];

        foreach ($searchCriteria as $criteriaName => $criteriaValue) {
                $whereParts[] = "$criteriaName=:$criteriaName";
                $whereParams[$criteriaName] = $criteriaValue;
        }

        $sql = <<<EOF
SELECT
 id_recipient, full_name, birthday, profession, amount, currency
FROM recipients
WHERE type = 'recipient'
EOF;


        if (0 < count($whereParts)) {
            $sql .= ' and ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $recipientData = $statement->fetchAll();

        $foundRecipients = [];

        foreach ($recipientData as $recipientItem) {
            $birthdayRecipient = DateTimeImmutable::createFromFormat('Y-m-d', $recipientItem['birthday']);
            $recipientItem['birthday'] = $birthdayRecipient->format('d.m.Y');
            $balance['currency'] = $recipientItem['currency'];
            $balance['amount'] = $recipientItem['amount'];
            $recipientItem['balance'] = $this->createBalanceData($balance);
            unset($recipientItem['currency'], $recipientItem['amount']);
            $recipientObj = Recipient::createFromArray($recipientItem);
            $foundRecipients[$recipientObj->getIdRecipient()] = $recipientObj;
        }
        return $foundRecipients;
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
