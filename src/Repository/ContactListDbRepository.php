<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\ContactList;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\Service\MoveToBlacklistService\Exception\RuntimeException;
use JsonException;

class ContactListDbRepository implements ContactListRepositoryInterface
{

    /**
     * Критерии поиска
     */
    private const ALLOWED_CRITERIA = [
        'id_entry',
        'id_recipient',
        'blacklist'
    ];

    /**
     *  Соединение с бд
     *
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;

    /**
     * @param ConnectionInterface $connection
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
            $errMsg = 'Неподдерживаемые критерии поиска блеклиста ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }


    /**
     *
     *
     * @param array $searchCriteria
     * @return array
     */
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
       id_entry, id_recipient, blacklist
FROM contact_list
EOF;

        if (0 < count($whereParts)) {
            $sql .= ' where ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $ContactListData = $statement->fetchAll();
        $foundContactList = [];
        foreach ($ContactListData as $ContactListItem) {
            $contactListObj = ContactList::createFromArray($ContactListItem);
            $foundContactList[$contactListObj->getIdEntry()] = $contactListObj;
        }

        return $foundContactList;
    }

    /**
     */
    public function save(ContactList $entity): ContactList
    {
        $sql = <<<EOF
UPDATE contact_list
SET 
    blacklist = :blacklist
WHERE id_recipient = :id_recipient
EOF;
        $values = [
            'id_recipient' => $entity->getIdRecipient(),
            'blacklist' => $entity->isBlackList()
        ];
        $this->connection->prepare($sql)->execute($values);
        return $entity;
    }

}
