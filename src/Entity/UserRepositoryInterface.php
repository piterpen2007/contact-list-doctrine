<?php

namespace EfTech\ContactList\Entity;

/**
 * Интерфейс репозитория для сущности юзер
 */
interface UserRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     */
    public function findBy(array $criteria):array;

    /** Поиск пользователя по логину 
     * @param string $login
     * @return User|null
     */
    public function findUserByLogin(string $login):?User;
}