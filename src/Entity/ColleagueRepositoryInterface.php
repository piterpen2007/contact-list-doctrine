<?php

namespace EfTech\ContactList\Entity;

interface ColleagueRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array;
}
