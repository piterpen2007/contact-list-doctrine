<?php

namespace EfTech\ContactList\Repository;

use Doctrine\ORM\EntityRepository;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;

class ContactListDoctrineRepository extends EntityRepository implements ContactListRepositoryInterface
{
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $contactId): array
    {
        return $this->findBy(['recipient' => $contactId]);
    }

}