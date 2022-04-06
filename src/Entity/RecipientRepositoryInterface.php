<?php

namespace EfTech\ContactList\Entity;

interface RecipientRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return Recipient[]
     */
    public function findBy(array $criteria): array;
}
