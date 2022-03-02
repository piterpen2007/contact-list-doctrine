<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ContactRepositoryInterface;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;

class ContactJsonRepository implements ContactRepositoryInterface
{
    /**
     *
     *
     * @var string
     */
    private string $pathToRecipients;
    /**
     *
     *
     * @var string
     */
    private string $pathToCustomers;
    /**
     *
     *
     * @var string
     */
    private string $pathToKinsfolk;
    /**
     *
     *
     * @var string
     */
    private string $pathToColleagues;

    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;

    /**
     * @param string $pathToRecipients
     * @param string $pathToCustomers
     * @param string $pathToKinsfolk
     * @param string $pathToColleagues
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(
        string $pathToRecipients,
        string $pathToCustomers,
        string $pathToKinsfolk,
        string $pathToColleagues,
        DataLoaderInterface $dataLoader
    ) {
        $this->pathToRecipients = $pathToRecipients;
        $this->pathToCustomers = $pathToCustomers;
        $this->pathToKinsfolk = $pathToKinsfolk;
        $this->pathToColleagues = $pathToColleagues;
        $this->dataLoader = $dataLoader;
    }

    /** Загружает данные о контактах по категориям
     * @return array
     */
    private function loadData(): array
    {
        $customers = $this->dataLoader->loadData($this->pathToCustomers);
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


    public function findBy(array $searchCriteria): array
    {
        $foundRecipientsOnCategory = [];
        $recipientsOnCategory = $this->loadData();
        if (array_key_exists('category', $searchCriteria)) {
            if ($searchCriteria['category'] === 'customers') {
                foreach ($recipientsOnCategory['customers'] as $customer) {
                    $customer['balance'] = $this->createBalancesData($customer);
                    $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
                }
            } elseif ($searchCriteria['category'] === 'recipients') {
                foreach ($recipientsOnCategory['recipients'] as $recipient) {
                    $recipient['balance'] = $this->createBalancesData($recipient);
                    $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
                }
            } elseif ($searchCriteria['category'] === 'kinsfolk') {
                foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                    $kinsfolkValue['balance'] = $this->createBalancesData($kinsfolkValue);
                    $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
                }
            } elseif ($searchCriteria['category'] === 'colleagues') {
                foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                    $colleague['balance'] = $this->createBalancesData($colleague);
                    $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
                }
            }
        } else {
            foreach ($recipientsOnCategory['customers'] as $customer) {
                $customer['balance'] = $this->createBalancesData($customer);
                $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
            }
            foreach ($recipientsOnCategory['recipients'] as $recipient) {
                $recipient['balance'] = $this->createBalancesData($recipient);
                $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
            }
            foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                $kinsfolkValue['balance'] = $this->createBalancesData($kinsfolkValue);
                $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
            }
            foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                $colleague['balance'] = $this->createBalancesData($colleague);
                $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
            }
        }
        return $foundRecipientsOnCategory;
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
