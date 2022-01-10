<?php

namespace EfTech\ContactList\Service\SearchCustomersService;

use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use JsonException;

class SearchCustomersService
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
    private string $pathToCustomers;

    /**
     * @param LoggerInterface $logger
     * @param string $pathToCustomers
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(LoggerInterface $logger ,string $pathToCustomers, DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
        $this->logger = $logger;
        $this->pathToCustomers = $pathToCustomers;
    }

    /**
     * @return array
     * @throws JsonException
     */
    private function loadData():array
    {
        return $this->dataLoader->loadData($this->pathToCustomers);
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
            $customer->getTimeToCall()
        );
    }

    /**
     * @param SearchCustomersCriteria $searchCriteria
     * @return CustomerDto[]
     */
    public function search(SearchCustomersCriteria $searchCriteria):array
    {
        $entitiesCollection = $this->searchEntity($searchCriteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->log( 'found recipients: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    /** Алгоритм поиска клиетов
     * @param SearchCustomersCriteria $searchCriteria
     * @return array
     * @throws JsonException
     */
    private function searchEntity(SearchCustomersCriteria $searchCriteria):array
    {
        $customers = $this->loadData();
        $foundCustomers = [];
        foreach ($customers as $customer) {
            if (null !== $searchCriteria->getIdRecipient()) {
                $customerMeetSearchCriteria = $searchCriteria->getIdRecipient() === $customer['id_recipient'];
            } else {
                $customerMeetSearchCriteria = true;
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getFullName()) {
                $customerMeetSearchCriteria = $searchCriteria->getFullName() === $customer['full_name'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getBirthday()) {
                $customerMeetSearchCriteria = $searchCriteria->getBirthday() === $customer['birthday'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getProfession()) {
                $customerMeetSearchCriteria = $searchCriteria->getProfession() === $customer['profession'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getContactNumber()) {
                $customerMeetSearchCriteria = $searchCriteria->getContactNumber() === $customer['contract_number'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getAverageTransactionAmount()) {
                $customerMeetSearchCriteria = $searchCriteria->getAverageTransactionAmount() === (string)$customer['average_transaction_amount'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getDiscount()) {
                $customerMeetSearchCriteria = $searchCriteria->getDiscount() === $customer['discount'];
            }
            if ($customerMeetSearchCriteria && null !== $searchCriteria->getTimeToCall()) {
                $customerMeetSearchCriteria = $searchCriteria->getTimeToCall() === $customer['time_to_call'];
            }

            if ($customerMeetSearchCriteria) {
                $foundCustomers[] = Customer::createFromArray($customer);
            }
        }
        $this->logger->log("Найдено клиентов : " . count($foundCustomers));
        return $foundCustomers;
    }
}