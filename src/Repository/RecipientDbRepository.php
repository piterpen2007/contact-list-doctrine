<?php

namespace EfTech\ContactList\Repository;

use DateTimeImmutable;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Email;
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


        $sql = <<<EOF
SELECT
       r.id_recipient as id_recipient,
       r.full_name as full_name,
       r.birthday as birthday,
       r.profession as profession,
       e.id as id_email,
       e.email as email,
       e.type_email as type_email 
FROM recipients as r
LEFT JOIN email e on r.id_recipient = e.recipient_id
WHERE type = 'recipient'
EOF;
        $whereParts = [];
        $whereParams = [];

        foreach ($searchCriteria as $criteriaName => $criteriaValue) {
                $whereParts[] = "$criteriaName=:$criteriaName";
                $whereParams[$criteriaName] = $criteriaValue;
        }
        if (0 < count($whereParts)) {
            $sql .= ' and ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $recipientData = $statement->fetchAll();

        $foundRecipients = [];

        foreach ($recipientData as $row) {
            if (false === array_key_exists($row['id_recipient'], $foundRecipients)) {
                $birthdayRecipient = DateTimeImmutable::createFromFormat('Y-m-d', $row['birthday']);

                $foundRecipients[$row['id_recipient']] = [
                    'id_recipient' => $row['id_recipient'],
                    'full_name' => $row['full_name'],
                    'birthday' => $birthdayRecipient,
                    'profession' => $row['profession'],
                    'emails' => [],
                ];
            }
            if (
                null !== $row['id_email']
                &&
                false === array_key_exists($row['id_email'], $foundRecipients[$row['id_recipient']]['emails'])
            ) {
                $obj = new Email(
                    $row['type_email'],
                    $row['email']
                );
                $foundRecipients[$row['id_recipient']]['emails'][$row['id_email']] = $obj;
            }

        }
        $recipientEntities = [];
        foreach ($foundRecipients as $item) {
            $recipientEntities[] = new Recipient(
                $item['id_recipient'],
                $item['full_name'],
                $item['birthday'],
                $item['profession'],
                $item['emails']
            );
        }
        return $recipientEntities;
    }


}
