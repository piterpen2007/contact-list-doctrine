<?php

namespace EfTech\ContactList\Entity;


interface ContactListRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $searchCriteria
     * @return array
     */
    public function findBy(array $searchCriteria):array;
    /** Сохранить сущность в репозитории
     * @param ContactList $entity
     * @return ContactList
     */
    public function save(ContactList $entity):ContactList;


}