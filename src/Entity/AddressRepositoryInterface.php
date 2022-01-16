<?php

namespace EfTech\ContactList\Entity;


interface AddressRepositoryInterface
{
    /** Поиск сущностей по заданному критерию
     *
     * @param array $criteria
     * @return Address[]
     */
    public function findBy(array $criteria):array;
    /** Выбирает следующий id для новой сущности
     * @return int
     */
    public function nextId():int;

    /** Добавляет новую сущность
     * @param Address $entity
     * @return Address
     */
    public function add(Address $entity):Address;
}