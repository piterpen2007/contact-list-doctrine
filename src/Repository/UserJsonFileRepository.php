<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\User;
use EfTech\ContactList\Entity\UserRepositoryInterface;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Auth\UserDataStorageInterface;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Repository\UserRepository\UserDataProvider;

/**
 * Реализация репозитория для юзера
 */
final class UserJsonFileRepository implements UserRepositoryInterface, UserDataStorageInterface
{
    /** Путь до файла с данными о пользователях
     * @var string
     */
    private string $pathToUsers;
    private DataLoaderInterface  $dataLoader;
    /** загруженные данные о пользователях
     * @var array|null
     */
    private ?array $data = null;

    /**
     * @param string $pathToUsers
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(string $pathToUsers, DataLoaderInterface $dataLoader)
    {
        $this->pathToUsers = $pathToUsers;
        $this->dataLoader = $dataLoader;
    }


    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array
    {
        $dataItems = $this->loadData();
        $foundEntities = [];
        foreach ($dataItems as $user) {
            if (false === is_array($user)) {
                throw new RuntimeException('Данные о пользователях должны быть массивом');
            }
            if (array_key_exists('login',$criteria)) {
                $userMeetsSearchCriteria = $criteria['login'] === $user['login'];
            } else {
                $userMeetsSearchCriteria = true;
            }
            if ($userMeetsSearchCriteria && array_key_exists('id',$criteria)) {
                $userMeetsSearchCriteria = $criteria['id'] === $user['id'];
            }
            if ($userMeetsSearchCriteria) {
                $entity = $this->createUSer($user);
                $foundEntities[] = $entity;
            }
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

    /** загружает данные
     * @return array
     */
    private function loadData():array
    {
        if (null === $this->data) {
            $this->data = $this->dataLoader->loadData($this->pathToUsers);
            if (false === is_array($this->data)) {
                throw new RuntimeException('Данные о пользователях должны быть массивом');
            }
        }
        return $this->data;

    }

    private function createUSer(array $user):User
    {
        $this->validateUserItem($user);
        return new UserDataProvider(
            $user['id'],
            $user['login'],
            $user['password'],
        );
    }

    private function validateUserItem(array $user):void
    {
        if (false === array_key_exists('id',$user)) {
            throw new RuntimeException('Нету id пользователя');
        }
        if (false === array_key_exists('login',$user)) {
            throw new RuntimeException('Нету login пользователя');
        }
        if (false === array_key_exists('password',$user)) {
            throw new RuntimeException('Нету password пользователя');
        }
        if (false === is_int($user['id'])) {
            throw new RuntimeException('id пользователя должен быть числом');
        }
        if (false === is_string($user['login'])) {
            throw new RuntimeException('login пользователя должен быть строкой');
        }
        if (false === is_string($user['password'])) {
            throw new RuntimeException('password пользователя должен быть строкой');
        }
    }
}