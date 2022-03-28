<?php

namespace EfTech\ContactList\Repository;

use DateTimeImmutable;
use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ColleagueRepositoryInterface;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Email;
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
        $sql = <<<EOF
SELECT
       r.id_recipient as id_recipient, 
       r.full_name as full_name,
       r.birthday as birthday, 
       r.profession as profession, 
       c.department as department, 
       c.position as position, 
       c.room_number as room_number,  
       e.id as id_email,
       e.email as email,
       e.type_email as type_email 
FROM recipients as r
JOIN colleagues c on r.id_recipient = c.id_recipient
LEFT JOIN email e on c.id_recipient = e.recipient_id
WHERE r.type = 'colleague'
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
        $colleagueData = $statement->fetchAll();

        $foundColleagues = [];

        foreach ($colleagueData as $row) {
            if (false === array_key_exists($row['id_recipient'], $foundColleagues)) {
                $birthdayRecipient = DateTimeImmutable::createFromFormat('Y-m-d', $row['birthday']);

                $foundColleagues[$row['id_recipient']] = [
                    'id_recipient' => $row['id_recipient'],
                    'full_name' => $row['full_name'],
                    'birthday' => $birthdayRecipient,
                    'profession' => $row['profession'],
                    'department' => $row['department'],
                    'position' => $row['position'],
                    'room_number' => $row['room_number'],
                    'emails' => [],
                ];
            }
            if (
                null !== $row['id_email']
                &&
                false === array_key_exists($row['id_email'], $foundColleagues[$row['id_recipient']]['emails'])
            ) {
                $obj = new Email(
                    $row['type_email'],
                    $row['email']
                );
                $foundColleagues[$row['id_recipient']]['emails'][$row['id_email']] = $obj;
            }
        }
        $recipientEntities = [];
        foreach ($foundColleagues as $item) {
            $recipientEntities[] = new Colleague(
                $item['id_recipient'],
                $item['full_name'],
                $item['birthday'],
                $item['profession'],
                $item['emails'],
                $item['department'],
                $item['position'],
                $item['room_number']
            );
        }
        return $recipientEntities;
    }

}
