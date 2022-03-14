<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\KinsfolkRepositoryInterface;
use EfTech\ContactList\Service\SearchKinsfolkService\KinsfolkDto;
use EfTech\ContactList\Service\SearchKinsfolkService\SearchKinsfolkCriteria;
use Psr\Log\LoggerInterface;

class SearchKinsfolkService
{
    /**
     * @var KinsfolkRepositoryInterface
     */
    private KinsfolkRepositoryInterface $kinsfolkRepository;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param KinsfolkRepositoryInterface $kinsfolkRepository
     */
    public function __construct(LoggerInterface $logger, KinsfolkRepositoryInterface $kinsfolkRepository)
    {
        $this->logger = $logger;
        $this->kinsfolkRepository = $kinsfolkRepository;
    }

    /**
     * Создание dto родни
     * @param Kinsfolk $kinsfolk
     * @return KinsfolkDto
     */
    private function createDto(Kinsfolk $kinsfolk): KinsfolkDto
    {
        return new KinsfolkDto(
            $kinsfolk->getIdRecipient(),
            $kinsfolk->getFullName(),
            $kinsfolk->getBirthday(),
            $kinsfolk->getProfession(),
            $kinsfolk->getStatus(),
            $kinsfolk->getRingtone(),
            $kinsfolk->getHotkey(),
            $kinsfolk->getBalance()
        );
    }


    /**
     * @param SearchKinsfolkCriteria $searchCriteria
     * @return KinsfolkDto[]
     */
    public function search(SearchKinsfolkCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->kinsfolkRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug('found kinsfolk: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchCriteriaToArray(SearchKinsfolkCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'id_recipient' => $searchCriteria->getIdRecipient(),
            'full_name' => $searchCriteria->getFullName(),
            'birthday' => $searchCriteria->getBirthday(),
            'profession' => $searchCriteria->getProfession(),
            'status' => $searchCriteria->getStatus(),
            'ringtone' => $searchCriteria->getRingtone(),
            'hotkey' => $searchCriteria->getHotkey()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
