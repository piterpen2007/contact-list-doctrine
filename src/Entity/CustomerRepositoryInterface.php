<?php

namespace EfTech\ContactList\Entity;

interface CustomerRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return Customer[]
     */
    public function findBy(array $searchCriteria): array;
}
