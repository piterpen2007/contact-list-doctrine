<?php

namespace EfTech\ContactList\Entity;

interface RecipientRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return Recipient[]
     */
    public function findBy(array $searchCriteria): array;
}
