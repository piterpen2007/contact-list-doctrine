<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\User;
use EfTech\ContactList\Entity\UserRepositoryInterface;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Auth\UserDataStorageInterface;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\Repository\UserRepository\UserDataProvider;

/**
 * Реализация репозитория для юзера
 */
final class UserDbRepository implements UserRepositoryInterface, UserDataStorageInterface
{
    private const ALLOWED_CRITERIA = [
        'login'
    ];

    private ConnectionInterface $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array
    {
        $this->validateCriteria($criteria);
        $sql = <<<EOF
        SELECT id, login, password FROM users 
EOF;
        $whereParts = [];
        foreach ($criteria as $fieldName => $fieldValue) {
            $whereParts[] = "$fieldName=:$fieldName";
        }
        if (count($whereParts) > 0) {
            $sql .= ' where ' . implode(' and ', $whereParts);
        }

        $statement = $this->connection->prepare($sql);
        if (false === $statement->execute($criteria)) {
            throw new RuntimeException(
                'Ошибка выполнения подготовленного запроса в репоиторий пользователя'
            );
        }

        $foundEntities = [];
        $dataFromDb = $statement->fetchAll();
        foreach ($dataFromDb as $item) {
            $foundEntities[] = new UserDataProvider($item['id'], $item['login'], $item['password']);
        }

        return $foundEntities;
    }

    /** Поиск пользователя по логину
     * @param string $login
     * @return User|null
     */
    public function findUserByLogin(string $login): ?UserDataProvider
    {
        $entities = $this->findBy(['login' => $login]);
        $countEntities = count($entities);

        if ($countEntities > 1) {
            throw new RuntimeException('Найдены пользователи с дублирубщимися логинами');
        }
        return (0 === $countEntities) ? null : current($entities);
    }

    /**
     *  Валидация критериев поиска
     *
     * @param array $criteria
     * @return void
     */
    private function validateCriteria(array $criteria): void
    {
        $invalidCriteria = array_diff(array_keys($criteria), self::ALLOWED_CRITERIA);
        if (count($invalidCriteria) > 0) {
            $errMsg = 'неподдерживаемые критерии поиска пользователей: ' . implode(', ', $invalidCriteria);
            throw new RuntimeException($errMsg);
        }
    }
}
