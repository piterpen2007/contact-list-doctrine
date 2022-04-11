<?php

namespace EfTech\ContactList\Entity;

use EfTech\ContactList\Repository\UserRepository\UserDataProvider;

/**
 * Интерфейс репозитория для сущности юзер
 */
interface UserRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     */
    public function findBy(array $criteria): array;

    /** Поиск пользователя по логину
     * @param string $login
     * @return UserDataProvider|null
     */
    public function findUserByLogin(string $login): ?UserDataProvider;
}
