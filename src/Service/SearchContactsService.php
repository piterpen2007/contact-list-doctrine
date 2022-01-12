<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchContactsService\ColleaguesDto;
use EfTech\ContactList\Service\SearchContactsService\ContactDto;
use EfTech\ContactList\Service\SearchContactsService\CustomerDto;
use EfTech\ContactList\Service\SearchContactsService\KinsfolkDto;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;
use EfTech\ContactList\Service\SearchContactsService\RecipientDto;

class SearchContactsService
{
    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     *
     *
     * @var string
     */
    private string $pathToRecipients;
    private string $pathToCustomers;
    private string $pathToColleagues;
    private string $pathToKinsfolk;

    /**
     * @param DataLoaderInterface $dataLoader
     * @param LoggerInterface $logger
     * @param string $pathToRecipients
     * @param string $pathToCustomers
     * @param string $pathToColleagues
     * @param string $pathToKinsfolk
     */
    public function __construct(
        DataLoaderInterface $dataLoader,
        LoggerInterface $logger,
        string $pathToRecipients,
        string $pathToCustomers,
        string $pathToColleagues,
        string $pathToKinsfolk
    ) {
        $this->dataLoader = $dataLoader;
        $this->logger = $logger;
        $this->pathToRecipients = $pathToRecipients;
        $this->pathToCustomers = $pathToCustomers;
        $this->pathToColleagues = $pathToColleagues;
        $this->pathToKinsfolk = $pathToKinsfolk;
    }


    /** Загружает данные о получателях по категориям
     * @return array
     */
    private function loadData():array
    {
        $customers =$this->dataLoader->loadData($this->pathToCustomers);
        $recipients = $this->dataLoader->loadData($this->pathToRecipients);
        $kinsfolk = $this->dataLoader->loadData($this->pathToKinsfolk);
        $colleague = $this->dataLoader->loadData($this->pathToColleagues);

        return [
            'customers' => $customers,
            'recipients' => $recipients,
            'kinsfolk' => $kinsfolk,
            'colleagues' => $colleague
        ];
    }

    /**
     * Создание dto контакта
     * @param object $contact
     */
    private function createDto(object $contact):object
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
    public function search(SearchContactsCriteria $searchCriteria):array
    {
        $entitiesCollection = $this->searchEntity($searchCriteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->log( 'found contacts: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchEntity(SearchContactsCriteria $searchCriteria):array
    {
        $foundRecipientsOnCategory = [];
        $recipientsOnCategory = $this->loadData();
        if (null !== $searchCriteria->getCategory()) {
            if ($searchCriteria->getCategory() === 'customers') {
                foreach ($recipientsOnCategory['customers'] as $customer) {
                    $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
                }
                $this->logger->log('dispatch category "customers"');
                $this->logger->log('found customers: ' . count($foundRecipientsOnCategory));
            } elseif ($searchCriteria->getCategory() === 'recipients') {
                foreach ($recipientsOnCategory['recipients'] as $recipient) {
                    $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
                }
                $this->logger->log('dispatch category "recipients"');
                $this->logger->log('found customers: ' . count($foundRecipientsOnCategory));
            } elseif ($searchCriteria->getCategory() === 'kinsfolk') {
                foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                    $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
                }
                $this->logger->log('dispatch category "kinsfolk"');
                $this->logger->log('found kinsfolk: ' . count($foundRecipientsOnCategory));
            } elseif ($searchCriteria->getCategory() === 'colleagues') {
                foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                    $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
                }
                $this->logger->log('dispatch category "colleagues"');
                $this->logger->log('found colleagues: ' . count($foundRecipientsOnCategory));
            }
        } else {
            foreach ($recipientsOnCategory['customers'] as $customer) {
                $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
            }
            foreach ($recipientsOnCategory['recipients'] as $recipient) {
                $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
            }
            foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
            }
            foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
            }
        }
        return $foundRecipientsOnCategory;
    }



}