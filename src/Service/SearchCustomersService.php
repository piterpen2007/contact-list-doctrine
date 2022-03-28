<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Service\SearchCustomersService\CustomerDto;
use EfTech\ContactList\Service\SearchCustomersService\SearchCustomersCriteria;

class SearchCustomersService
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, LoggerInterface $logger)
    {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }


    /**
     * Создание dto клиента
     * @param Customer $customer
     * @return CustomerDto
     */
    private function createDto(Customer $customer): CustomerDto
    {
        return new CustomerDto(
            $customer->getIdRecipient(),
            $customer->getFullName(),
            $customer->getBirthday(),
            $customer->getProfession(),
            $customer->getContractNumber(),
            $customer->getAverageTransactionAmount(),
            $customer->getDiscount(),
            $customer->getTimeToCall(),
            $customer->getEmails()
        );
    }

    /**
     * @param SearchCustomersCriteria $searchCriteria
     * @return CustomerDto[]
     */
    public function search(SearchCustomersCriteria $searchCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($searchCriteria);
        $entitiesCollection = $this->customerRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug('found customers: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    private function searchCriteriaToArray(SearchCustomersCriteria $searchCriteria): array
    {
        $criteriaForRepository = [
            'id_recipient' => $searchCriteria->getIdRecipient(),
            'full_name' => $searchCriteria->getFullName(),
            'birthday' => $searchCriteria->getBirthday(),
            'profession' => $searchCriteria->getProfession(),
            'contract_number' => $searchCriteria->getContactNumber(),
            'average_transaction_amount' => $searchCriteria->getAverageTransactionAmount(),
            'discount' => $searchCriteria->getDiscount(),
            'time_to_call' => $searchCriteria->getTimeToCall()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
