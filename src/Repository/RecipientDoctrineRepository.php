<?php

namespace EfTech\ContactList\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;

class RecipientDoctrineRepository extends EntityRepository implements RecipientRepositoryInterface
{
    private const CRITERIA_REPLACE = [
        'id_recipient' => 'r.id_recipient',
        'full_name' => 'r.full_name',
        'birthday' => 'r.birthday',
        'profession' => 'r.profession',
        'id_recipient_list' => 'id_recipient'
    ];


    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteriaForDefinitionsType = $this->prepareCriteria($criteria);

        if (array_key_exists('id_recipient_list', $criteria)) {
            return parent::findBy($criteriaForDefinitionsType, $orderBy, $limit, $offset);
        }

        $recipientsData = [];
        if (0 === count($criteriaForDefinitionsType)) {
            $recipientsData = $this->loadRecipient($criteria);
        } else {
            foreach ($criteriaForDefinitionsType as $name => $value) {
                $recipientsData = $this->loadRecipient($criteria, $name);
            }
        }

        return $recipientsData;
    }


    private function prepareCriteria(array $criteria): array
    {
        if (0 === count($criteria)) {
            return [];
        }

        $preparedCriteria = [];
        foreach ($criteria as $criteriaName => $criteriaValue) {
            if (array_key_exists($criteriaName, self::CRITERIA_REPLACE)) {
                $preparedCriteria[self::CRITERIA_REPLACE[$criteriaName]] = $criteriaValue;
            }
        }
        return empty($preparedCriteria) ? $criteria : $preparedCriteria;
    }


    /**
     * Загрузка данных о знакомых
     *
     * @param array $preparedCriteria
     * @param string|null $alias
     *
     * @return array
     */
    private function loadRecipient(array $preparedCriteria, string $alias = null): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select(['r'])
            ->from(Recipient::class, 'r');

        $this->buildWhere($preparedCriteria, $queryBuilder, $alias);

        return $queryBuilder->getQuery()->getResult();
    }

    private function buildWhere(array $preparedCriteria, QueryBuilder $queryBuilder, ?string $alias): void
    {
//        if (0 === count($preparedCriteria)) {
//            return;
//        }

        $whereExprAnd = $queryBuilder->expr()->andX();
        foreach ($preparedCriteria as $name => $value) {
            $whereExprAnd->add($queryBuilder->expr()->eq($alias, ":$name"));
        }
        $whereExprAnd->add($queryBuilder->expr()
            ->not($queryBuilder->expr()->isInstanceOf('r', Customer::class)));
        $whereExprAnd->add($queryBuilder->expr()
            ->not($queryBuilder->expr()->isInstanceOf('r', Colleague::class)));
        $whereExprAnd->add($queryBuilder->expr()
            ->not($queryBuilder->expr()->isInstanceOf('r', Kinsfolk::class)));
        $queryBuilder->where($whereExprAnd);
        $queryBuilder->setParameters($preparedCriteria);
    }
}
