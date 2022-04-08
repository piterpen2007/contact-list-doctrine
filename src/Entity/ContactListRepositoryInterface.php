<?php

namespace EfTech\ContactList\Entity;

interface ContactListRepositoryInterface
{
    /**
     * Поиск контакта в контактном листе
     *
     * @param int $contactId
     *
     * @return Recipient[]
     */
    public function findById(int $contactId): array;

}
