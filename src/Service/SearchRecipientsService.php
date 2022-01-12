<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\invalidDataStructureException;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsCriteria;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\ValueObject\Currency;
use EfTech\ContactList\ValueObject\Money;
use JsonException;

class SearchRecipientsService
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

    /**
     * @param LoggerInterface $logger
     * @param string $pathToRecipients
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(LoggerInterface $logger ,string $pathToRecipients, DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
        $this->logger = $logger;
        $this->pathToRecipients = $pathToRecipients;
    }

    /**
     * @return array
     */
    private function loadData():array
    {
        return $this->dataLoader->loadData($this->pathToRecipients);
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
        );
    }


    /**
     * @param SearchRecipientsCriteria $searchCriteria
     * @return RecipientDto[]
     * @throws JsonException
     */
    public function search(SearchRecipientsCriteria $searchCriteria):array
    {
        $entitiesCollection = $this->searchEntity($searchCriteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->log( 'found recipients: ' . count($entitiesCollection));
        return $dtoCollection;
    }

    /** Алгоритм поиска получателей
     * @param SearchRecipientsCriteria $searchCriteria
     * @return array
     * @throws JsonException
     */
    private function searchEntity(SearchRecipientsCriteria $searchCriteria):array
    {
        $recipients = $this->loadData();
        $findRecipient = [];
        foreach ($recipients as $recipient) {
            if (null !== $searchCriteria->getIdRecipient()) {
                $recipientMeetSearchCriteria = $searchCriteria->getIdRecipient() === $recipient['id_recipient'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if ($recipientMeetSearchCriteria && null !== $searchCriteria->getFullName()) {
                $recipientMeetSearchCriteria = $searchCriteria->getFullName() === $recipient['full_name'];
            }
            if ($recipientMeetSearchCriteria && null !== $searchCriteria->getBirthday()) {
                $recipientMeetSearchCriteria = $searchCriteria->getBirthday() === $recipient['birthday'];
            }
            if ($recipientMeetSearchCriteria && null !== $searchCriteria->getProfession()) {
                $recipientMeetSearchCriteria = $searchCriteria->getProfession() === $recipient['profession'];
            }
            if ($recipientMeetSearchCriteria) {
                $recipient['balance'] = $this->createBalancesData($recipient);
                $findRecipient[] = Recipient::createFromArray($recipient);
            }
        }
        $this->logger->log("Найдено получателей : " . count($findRecipient));
        return $findRecipient;
    }

    private function createBalanceData($balances):Balance
    {
        if (false === is_array($balances)) {
            throw new InvalidDataStructureException('Данные о балансе имеют невалидный формат');
        }
        if (false === array_key_exists('amount',$balances)) {
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
    private function createBalancesData(array $recipients):array
    {
        if(false === array_key_exists('balance',$recipients)) {
            throw new InvalidDataStructureException('Нет данных о балансе');
        }
        if(false === is_array($recipients['balance'])) {
            throw new InvalidDataStructureException('Данные о балансе имею неверный формат');
        }
        $balancesData = [];
        foreach ($recipients['balance'] as $balanceData) {
            $balancesData[] = $this->createBalanceData($balanceData);
        }
        return $balancesData;
    }
}