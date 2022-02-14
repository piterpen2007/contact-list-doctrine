<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\ContactList;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Service\SearchContactListService\ContactListDto;
use EfTech\ContactList\Service\SearchContactListService\SearchContactListCriteria;

class SearchContactListService
{
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /** Наш репозиторий черного списка
     * @var ContactListRepositoryInterface
     */
    private ContactListRepositoryInterface $contactListRepository;

    /**
     * @param LoggerInterface $logger
     * @param ContactListRepositoryInterface $contactListRepository
     */
    public function __construct(LoggerInterface $logger, ContactListRepositoryInterface $contactListRepository)
    {
        $this->logger = $logger;
        $this->contactListRepository = $contactListRepository;
    }

    /**
     * @param ContactList $contactList
     * @return ContactListDto
     */
    private function createDto(ContactList $contactList): ContactListDto
    {
        return new ContactListDto(
            $contactList->getIdRecipient(),
            $contactList->getIdEntry(),
            $contactList->isBlackList()
        );
    }

    /**
     *
     *
     * @param SearchContactListCriteria $searchCriteria
     * @return ContactListDto[]
     */
    public function search(SearchContactListCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->contactListRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug("Найдено contact_list: " . count($entitiesCollection));
        return $dtoCollection;
    }

    /** Преобразует критерии поиска в массив
     * @param SearchContactListCriteria $searchCriteria
     * @return array
     */
    private function searchCriteriaToArray(SearchContactListCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'id_recipient' => $searchCriteria->getIdRecipient(),
            'id_entry' => $searchCriteria->getIdEntry(),
            'blacklist' => $searchCriteria->getBlacklist()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
