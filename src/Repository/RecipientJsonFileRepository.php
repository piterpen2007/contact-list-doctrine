<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;

class RecipientJsonFileRepository implements RecipientRepositoryInterface
{
    /** Данные о получателях
     * @var array|null
     */
    private ?array $data = null;
    /**
     *
     *
     * @var string
     */
    private string $pathToRecipients;

    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;

    /**
     * @param string $pathToRecipients
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(string $pathToRecipients, DataLoaderInterface $dataLoader)
    {
        $this->pathToRecipients = $pathToRecipients;
        $this->dataLoader = $dataLoader;
    }

    /**
     * @return array
     */
    private function loadData(): array
    {
        if (null === $this->data) {
            $this->data = $this->dataLoader->loadData($this->pathToRecipients);
        }
        return $this->data;
    }


    public function findBy(array $searchCriteria): array
    {
        $recipients = $this->loadData();
        $findRecipient = [];
        foreach ($recipients as $recipient) {
            if (array_key_exists('id_recipient', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['id_recipient'] === $recipient['id_recipient'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if ($recipientMeetSearchCriteria && array_key_exists('full_name', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['full_name'] === $recipient['full_name'];
            }
            if ($recipientMeetSearchCriteria && array_key_exists('birthday', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['birthday'] === $recipient['birthday'];
            }
            if ($recipientMeetSearchCriteria && array_key_exists('profession', $searchCriteria)) {
                $recipientMeetSearchCriteria = $searchCriteria['profession'] === $recipient['profession'];
            }
            if ($recipientMeetSearchCriteria) {
                $recipient['balance'] = $this->createBalancesData($recipient);
                $findRecipient[] = Recipient::createFromArray($recipient);
            }
        }
        return $findRecipient;
    }


    private function createBalanceData($balances): Balance
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
     * @return Balance[]
     */
    private function createBalancesData(array $recipients): array
    {
        if (false === array_key_exists('balance', $recipients)) {
            throw new InvalidDataStructureException('Нет данных о балансе');
        }
        if (false === is_array($recipients['balance'])) {
            throw new InvalidDataStructureException('Данные о балансе имею неверный формат');
        }
        $balancesData = [];
        foreach ($recipients['balance'] as $balanceData) {
            $balancesData[] = $this->createBalanceData($balanceData);
        }
        return $balancesData;
    }
}
