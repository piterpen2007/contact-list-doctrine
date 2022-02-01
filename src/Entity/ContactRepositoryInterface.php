<?php

namespace EfTech\ContactList\Entity;

interface ContactRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return array
     */
    public function findBy(array $searchCriteria): array;
}
