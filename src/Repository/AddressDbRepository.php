<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use JsonException;

class AddressDbRepository implements AddressRepositoryInterface
{
    /**
     * Критерии поиска
     */
    private const ALLOWED_CRITERIA = [
        'id_address',
        'id_recipient',
        'address',
        'status'
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
            $errMsg = 'Неподдерживаемые критерии поиска адресов ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }

    public function findBy(array $criteria): array
    {
        $this->validateCriteria($criteria);

        $whereParts = [];
        $whereParams = [];

        foreach ($criteria as $criteriaName => $criteriaValue) {
            $whereParts[] = "$criteriaName=:$criteriaName";
            $whereParams[$criteriaName] = $criteriaValue;
        }
        $sql = <<<EOF
SELECT
       id_address, id_recipient, address, status
FROM address
EOF;

        if (0 < count($whereParts)) {
            $sql .= ' where ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($whereParams);
        $addressesData = $statement->fetchAll();
        $foundAddress = [];
        foreach ($addressesData as $addressItem) {
            $addressObj = Address::createFromArray($addressItem);
            $foundAddress[$addressObj->getIdAddress()] = $addressObj;
        }

        return $foundAddress;
    }

    public function nextId(): int
    {
        $sql = <<<EOF
SELECT nextval('address_id_address_seq') AS next_id
EOF;

        return (int)current($this->connection->query($sql)->fetchAll())['next_id'];
    }

    /**
     *
     *
     * @param Address $entity
     * @return Address
     */
    public function add(Address $entity): Address
    {
        $sql = <<<EOF
INSERT INTO address ( id_address ,id_recipient, address, status)
VALUES (
        :id_address, :id_recipient, :address, :status
)
EOF;
        $values = [
            'id_address' => $entity->getIdAddress(),
            'id_recipient' => $entity->getIdRecipient(),
            'address' => $entity->getAddress(),
            'status' => $entity->getStatus()
        ];
//
//        $this->loadData();
//        $item = $this->buildJsonDataAddress($entity);
//        $this->addressData[] = $item;
//        $data = $this->addressData;
//        $file = $this->pathToAddress;
//
//        $jsonStr = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//        file_put_contents($file, $jsonStr);
        $this->connection->prepare($sql)->execute($values);
        return $entity;
    }
}
