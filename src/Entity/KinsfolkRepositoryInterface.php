<?php

namespace EfTech\ContactList\Entity;

interface KinsfolkRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return Kinsfolk[]
     */
    public function findBy(array $searchCriteria): array;
}
