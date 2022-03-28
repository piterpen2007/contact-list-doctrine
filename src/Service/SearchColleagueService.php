<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ColleagueRepositoryInterface;
use EfTech\ContactList\Service\SearchColleagueService\ColleagueDto;
use EfTech\ContactList\Service\SearchColleagueService\SearchColleagueCriteria;
use Psr\Log\LoggerInterface;

class SearchColleagueService
{
    /**
     * @var ColleagueRepositoryInterface
     */
    private ColleagueRepositoryInterface $colleagueRepository;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param ColleagueRepositoryInterface $colleagueRepository
     */
    public function __construct(LoggerInterface $logger, ColleagueRepositoryInterface $colleagueRepository)
    {
        $this->logger = $logger;
        $this->colleagueRepository = $colleagueRepository;
    }

    /**
     * Создание dto родни
     * @param Colleague $colleague
     * @return ColleagueDto
     */
    private function createDto(Colleague $colleague): ColleagueDto
    {
        return new ColleagueDto(
            $colleague->getIdRecipient(),
            $colleague->getFullName(),
            $colleague->getBirthday(),
            $colleague->getProfession(),
            $colleague->getDepartment(),
            $colleague->getPosition(),
            $colleague->getRoomNumber(),
            $colleague->getEmails()
        );
    }


    /**
     * @param SearchColleagueCriteria $searchCriteria
     * @return ColleagueDto[]
     */
    public function search(SearchColleagueCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->colleagueRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug('found kinsfolk: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchCriteriaToArray(SearchColleagueCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'id_recipient' => $searchCriteria->getIdRecipient(),
            'full_name' => $searchCriteria->getFullName(),
            'birthday' => $searchCriteria->getBirthday(),
            'profession' => $searchCriteria->getProfession(),
            'department' => $searchCriteria->getDepartment(),
            'position' => $searchCriteria->getPosition(),
            'room_number' => $searchCriteria->getRoomNumber()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
