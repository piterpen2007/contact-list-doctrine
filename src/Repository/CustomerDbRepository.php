<?php

namespace EfTech\ContactList\Repository;

use DateTimeImmutable;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\CustomerRepositoryInterface;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Email;
use EfTech\ContactList\ValueObject\Money;

class CustomerDbRepository implements CustomerRepositoryInterface
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
        'contract_number' => 'c.contract_number',
        'average_transaction_amount' => 'c.average_transaction_amount',
        'discount' => 'c.discount',
        'time_to_call' => 'c.time_to_call'
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
            $errMsg = 'Неподдерживаемые критерии поиска клиентов ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }


    public function findBy(array $searchCriteria): array
    {
        $this->validateCriteria($searchCriteria);


        $sql = <<<EOF
SELECT
       r.id_recipient as id_recipient, 
       r.full_name as full_name,
       r.birthday as birthday, 
       r.profession as profession, 
       c.contract_number as contract_number, 
       c.average_transaction_amount as average_transaction_amount, 
       c.discount as discount, 
       c.time_to_call as time_to_call, 
       e.id as id_email,
       e.email as email,
       e.type_email as type_email 
FROM recipients as r
JOIN customers c on r.id_recipient = c.id_recipient
LEFT JOIN email e on c.id_recipient = e.recipient_id
WHERE type = 'customer'
EOF;


        $whereParts = [];
        $whereParams = [];

        foreach ($searchCriteria as $criteriaName => $criteriaValue) {
            $criteriaToSqlParts = self::ALLOWED_CRITERIA;
            $whereParts[] = "{$criteriaToSqlParts[$criteriaName]}=:$criteriaName";
            $whereParams[$criteriaName] = $criteriaValue;
        }

        if (0 < count($whereParts)) {
            $sql .= ' and ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $customerData = $statement->fetchAll();

        $foundCustomers = [];

        foreach ($customerData as $row) {
            if (false === array_key_exists($row['id_recipient'], $foundCustomers)) {
                $birthdayRecipient = DateTimeImmutable::createFromFormat('Y-m-d', $row['birthday']);

                $foundCustomers[$row['id_recipient']] = [
                    'id_recipient' => $row['id_recipient'],
                    'full_name' => $row['full_name'],
                    'birthday' => $birthdayRecipient,
                    'profession' => $row['profession'],
                    'contract_number' => $row['contract_number'],
                    'average_transaction_amount' => $row['average_transaction_amount'],
                    'discount' => $row['discount'],
                    'time_to_call' => $row['time_to_call'],
                    'emails' => [],
                ];
            }
            if (
                null !== $row['id_email']
                &&
                false === array_key_exists($row['id_email'], $foundCustomers[$row['id_recipient']]['emails'])
            ) {
                $obj = new Email(
                    $row['type_email'],
                    $row['email']
                );
                $foundCustomers[$row['id_recipient']]['emails'][$row['id_email']] = $obj;
            }
        }
        $recipientEntities = [];
        foreach ($foundCustomers as $item) {
            $recipientEntities[] = new Customer(
                $item['id_recipient'],
                $item['full_name'],
                $item['birthday'],
                $item['profession'],
                $item['emails'],
                $item['contract_number'],
                $item['average_transaction_amount'],
                $item['discount'],
                $item['time_to_call']
            );
        }
        return $recipientEntities;
    }
}
