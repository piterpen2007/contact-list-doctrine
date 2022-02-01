<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ContactRepositoryInterface;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchContactsService\ColleaguesDto;
use EfTech\ContactList\Service\SearchContactsService\ContactDto;
use EfTech\ContactList\Service\SearchContactsService\CustomerDto;
use EfTech\ContactList\Service\SearchContactsService\KinsfolkDto;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;
use EfTech\ContactList\Service\SearchContactsService\RecipientDto;

class SearchContactsService
{
    private ContactRepositoryInterface $contactRepository;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ContactRepositoryInterface $contactRepository
     * @param LoggerInterface $logger
     */
    public function __construct(ContactRepositoryInterface $contactRepository, LoggerInterface $logger)
    {
        $this->contactRepository = $contactRepository;
        $this->logger = $logger;
    }

    /**
     * Создание dto контакта
     * @param object $contact
     * @return object
     */
    private function createDto(object $contact): object
    {
        if ($contact instanceof Customer) {
            return new CustomerDto(
                $contact->getIdRecipient(),
                $contact->getFullName(),
                $contact->getBirthday(),
                $contact->getProfession(),
                $contact->getContractNumber(),
                $contact->getAverageTransactionAmount(),
                $contact->getDiscount(),
                $contact->getTimeToCall()
            );
        }

        if ($contact instanceof Kinsfolk) {
            return new KinsfolkDto(
                $contact->getIdRecipient(),
                $contact->getFullName(),
                $contact->getBirthday(),
                $contact->getProfession(),
                $contact->getStatus(),
                $contact->getRingtone(),
                $contact->getHotkey()
            );
        }

        if ($contact instanceof Colleague) {
            return new ColleaguesDto(
                $contact->getIdRecipient(),
                $contact->getFullName(),
                $contact->getBirthday(),
                $contact->getProfession(),
                $contact->getDepartment(),
                $contact->getPosition(),
                $contact->getRoomNumber()
            );
        }
            return new RecipientDto(
                $contact->getIdRecipient(),
                $contact->getFullName(),
                $contact->getBirthday(),
                $contact->getProfession()
            );
    }

    /**
     * @param SearchContactsCriteria $searchCriteria
     * @return ContactDto[]
     */
    public function search(SearchContactsCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->contactRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug('found contacts: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchCriteriaToArray(SearchContactsCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'category' => $searchCriteria->getCategory()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
