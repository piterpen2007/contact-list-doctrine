<?php

namespace EfTech\ContactList\Entity;

interface CustomerRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array;
}
