<?php

namespace EfTech\ContactList\Entity;

interface ColleagueRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return Colleague[]
     */
    public function findBy(array $searchCriteria): array;
}
