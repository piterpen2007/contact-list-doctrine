<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsCriteria;

class SearchRecipientsService
{
    /**
     * @var RecipientRepositoryInterface
     */
    private RecipientRepositoryInterface $recipientRepository;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param RecipientRepositoryInterface $recipientRepository
     */
    public function __construct(LoggerInterface $logger, RecipientRepositoryInterface $recipientRepository)
    {
        $this->logger = $logger;
        $this->recipientRepository = $recipientRepository;
    }
    /**
     * Создание dto Получателя
     * @param Recipient $recipient
     * @return RecipientDto
     */
    private function createDto(Recipient $recipient): RecipientDto
    {
        return new RecipientDto(
            $recipient->getIdRecipient(),
            $recipient->getFullName(),
            $recipient->getBirthday(),
            $recipient->getProfession(),
            $recipient->getBalance()
        );
    }


    /**
     * @param SearchRecipientsCriteria $searchCriteria
     * @return RecipientDto[]
     */
    public function search(SearchRecipientsCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->recipientRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug('found recipients: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchCriteriaToArray(SearchRecipientsCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'id_recipient' => $searchCriteria->getIdRecipient(),
            'full_name' => $searchCriteria->getFullName(),
            'birthday' => $searchCriteria->getBirthday(),
            'profession' => $searchCriteria->getProfession()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
