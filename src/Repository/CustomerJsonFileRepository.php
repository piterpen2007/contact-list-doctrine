<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\CustomerRepositoryInterface;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;

class CustomerJsonFileRepository implements CustomerRepositoryInterface
{
    /** Данные о клиентах
     * @var array|null
     */
    private ?array $data = null;
    /**
     *
     *
     * @var string
     */
    private string $pathToCustomers;

    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;

    /**
     * @param string $pathToCustomers
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(string $pathToCustomers, DataLoaderInterface $dataLoader)
    {
        $this->pathToCustomers = $pathToCustomers;
        $this->dataLoader = $dataLoader;
    }
    /**
     * @return array
     */
    private function loadData(): array
    {
        if (null === $this->data) {
            $this->data = $this->dataLoader->loadData($this->pathToCustomers);
        }
        return $this->data;
    }


    public function findBy(array $searchCriteria): array
    {
        $customers = $this->loadData();
        $foundCustomers = [];
        foreach ($customers as $customer) {
            if (array_key_exists('id_recipient', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['id_recipient'] === $customer['id_recipient'];
            } else {
                $customerMeetSearchCriteria = true;
            }
            if ($customerMeetSearchCriteria && array_key_exists('full_name', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['full_name'] === $customer['full_name'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('birthday', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['birthday'] === $customer['birthday'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('profession', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['profession'] === $customer['profession'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('contract_number', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['contract_number'] === $customer['contract_number'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('average_transaction_amount', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['average_transaction_amount']
                    === (string)$customer['average_transaction_amount'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('discount', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['discount'] === $customer['discount'];
            }
            if ($customerMeetSearchCriteria && array_key_exists('time_to_call', $searchCriteria)) {
                $customerMeetSearchCriteria = $searchCriteria['time_to_call'] === $customer['time_to_call'];
            }

            if ($customerMeetSearchCriteria) {
                $customer['balance'] = $this->createBalancesData($customer);
                $foundCustomers[] = Customer::createFromArray($customer);
            }
        }
        return $foundCustomers;
    }

    private function createBalanceData(array $balances): Balance
    {
        if (false === is_array($balances)) {
            throw new InvalidDataStructureException('Данные о балансе имеют невалидный формат');
        }
        if (false === array_key_exists('amount', $balances)) {
            throw new InvalidDataStructureException('Отсутствуют данные о деньгах на балансе');
        }
        if (false === is_int($balances['amount'])) {
            throw new InvalidDataStructureException('Данные о самом балансе имеют неверный формат');
        }
        if (false === array_key_exists('currency', $balances)) {
            throw new InvalidDataStructureException('Отсутствуют данные о валюте');
        }
        if (false === is_string($balances['currency'])) {
            throw new InvalidDataStructureException('Данные о валюте имеют не верный формат');
        }
        $currencyName = 'RUB' === $balances['currency'] ? 'рубль' : 'неизвестно';
        return new Balance(
            new Money(
                $balances['amount'],
                new Currency($balances['currency'], $currencyName)
            )
        );
    }
    /**
     * - " "
     *
     * @param array $recipients
     *
     * @return Balance
     */
    private function createBalancesData(array $recipients): Balance
    {
        if (false === array_key_exists('balance', $recipients)) {
            throw new InvalidDataStructureException('Нет данных о балансе');
        }
        if (false === is_array($recipients['balance'])) {
            throw new InvalidDataStructureException('Данные о балансе имею неверный формат');
        }
        return $this->createBalanceData($recipients['balance']);
    }
}
