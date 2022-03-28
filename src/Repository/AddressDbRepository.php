<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\Address\Status;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;

/**
 *  Репозиторий адресов
 */
class AddressDbRepository implements AddressRepositoryInterface
{
    private const BASE_SEARCH_SQL = <<<EOF
SELECT a.id_address                 AS id_address,
       a.address                    AS address,
       ads.name                     AS status,
       c.id_recipient               AS id_recipient
FROM address AS a
         LEFT JOIN address_to_recipients as atr on atr.id_address = a.id_address
         LEFT JOIN recipients as c ON c.id_recipient = atr.id_recipient
         LEFT JOIN address_status as ads on ads.id = a.status_id
EOF;

    /**
     * Репозиторий для работы с получателями
     *
     * @var RecipientDbRepository
     */
    private RecipientDbRepository $recipientDbRepository;

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
     * @param RecipientDbRepository $recipientDbRepository
     */
    public function __construct(ConnectionInterface $connection, RecipientDbRepository $recipientDbRepository)
    {
        $this->connection = $connection;
        $this->recipientDbRepository = $recipientDbRepository;
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

        $sql = self::BASE_SEARCH_SQL;
        $addressData = $this->connection->query($sql)->fetchAll();

        return $this->buildEntity($addressData);
    }

    public function nextId(): int
    {
        $sql = <<<EOF
SELECT nextval('address_id_address_seq') AS next_id
EOF;

        return (int)current($this->connection->query($sql)->fetchAll())['next_id'];
    }

    /**
     * Добавление новой сущности в бд
     *
     * @param Address $entity
     * @return Address
     */
    public function add(Address $entity): Address
    {
        $sql = <<<EOF
INSERT INTO address (id_address ,address, status_id) 
(
    SELECT :id_address, :address, ads.id
    FROM address_status AS ads
    WHERE ads.name = :status          
)
EOF;

        $values = [
            'id_address' => $entity->getIdAddress(),
            'address' => $entity->getAddress(),
            'status' => $entity->getStatus()
        ];

        $this->connection->prepare($sql)->execute($values);

        $this->saveAddressToContact($entity);

        return $entity;
    }

    /**
     * Создание сущности Адрес
     *
     * @param array $data
     * @return array
     */
    private function buildEntity(array $data): array
    {
        $addressData = [];
        $contact = [];
        foreach ($data as $row) {
            if (false === array_key_exists($row['id_address'], $addressData)) {
                $addressData[$row['id_address']] = [
                    'id_address' => $row['id_address'],
                    'id_recipient' => [],
                    'address' => $row['address'],
                    'status' => $row['status'],
                ];
            }

            if (null !== $row['id_recipient']) {
                if (false === array_key_exists($row['id_recipient'], $contact)) {
                    $contact[$row['id_recipient']] = $row['id_recipient'];
                }
                $addressData[$row['id_address']]['id_recipient'][] = $contact[$row['id_recipient']];
            }
        }
        $addressEntities = [];
        foreach ($addressData as $item) {
            $addressData[$item['id_address']]['status'] = new Status($addressData[$item['id_address']]['status']);
            $addressEntities[] = Address::createFromArray($addressData[$item['id_address']]);
        }
        return $addressEntities;
    }

    private function saveAddressToContact(Address $entity): void
    {
        $this->connection
            ->prepare('DELETE FROM address_to_recipients WHERE id_address = :addressId')
            ->execute(['addressId' => $entity->getIdAddress()]);

        $insertParts = [];
        $insertParams = [];
        foreach ($entity->getIdRecipient() as $index => $contact) {
            $insertParts[] = "(:addressId_$index, :recipientId_$index)";
            $insertParams["addressId_$index"] = $entity->getIdAddress();
            $insertParams["recipientId_$index"] = $contact;
        }

        if (count($insertParts) > 0) {
            $values = implode(', ', $insertParts);

            $sql = <<<EOF
INSERT INTO address_to_recipients(id_address, id_recipient) VALUES $values
EOF;
            $this->connection->prepare($sql)->execute($insertParams);
        }
    }
}
