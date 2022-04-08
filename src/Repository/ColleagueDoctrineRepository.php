<?php

namespace EfTech\ContactList\Repository;

use Doctrine\ORM\EntityRepository;
use EfTech\ContactList\Entity\ColleagueRepositoryInterface;

class ColleagueDoctrineRepository extends EntityRepository implements ColleagueRepositoryInterface
{
    private const CRITERIA_REPLACE = [
        'id_recipient' => 'id_recipient',
        'full_name' => 'full_name',
        'birthday' => 'birthday',
        'profession' => 'profession',
        'department' => 'department',
        'position' => 'position',
        'room_number' => 'roomNumber'
    ];


    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteriaForDefinitionsType = $this->prepareCriteria($criteria);

        return parent::findBy($criteriaForDefinitionsType, $orderBy, $limit, $offset);
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
}
