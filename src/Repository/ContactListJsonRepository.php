<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\ContactList;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Service\MoveToBlacklistService\Exception\RuntimeException;
use JsonException;

class ContactListJsonRepository implements ContactListRepositoryInterface
{
    /**
     *
     *
     * @var string
     */
    private string $pathToContactList;
    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;
    /** данные о контактном списке
     * @var array|null
     */
    private ?array $contactListData = null;

    /** Сопоставляет id онтакта с номером элемента в $contactListData
     * @var array|null
     */
    private ?array $contactListIdToIndex = null;

    /**
     * @param string $pathToContactList
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(string $pathToContactList, DataLoaderInterface $dataLoader)
    {
        $this->pathToContactList = $pathToContactList;
        $this->dataLoader = $dataLoader;
    }

    /**
     * @return array
     */
    private function loadData(): array
    {
        if (null === $this->contactListData) {
            $this->contactListData = $this->dataLoader->loadData($this->pathToContactList);
            $this->contactListIdToIndex = array_combine(
                array_map(static function (array $v) {
                    return $v['id_recipient'];
                }, $this->contactListData),
                array_keys($this->contactListData)
            );
        }
        return $this->contactListData;
    }


    public function findBy(array $searchCriteria): array
    {
        $contactLists = $this->loadData();
        $findContactList = [];
        foreach ($contactLists as $contactList) {
            if (array_key_exists('id_recipient', $searchCriteria)) {
                $contactListMeetSearchCriteria = $searchCriteria['id_recipient'] === $contactList['id_recipient'];
            } else {
                $contactListMeetSearchCriteria = true;
            }
            if ($contactListMeetSearchCriteria && array_key_exists('id_entry', $searchCriteria)) {
                $contactListMeetSearchCriteria = $searchCriteria['id_entry'] === $contactList['id_entry'];
            }
            if ($contactListMeetSearchCriteria && array_key_exists('blacklist', $searchCriteria)) {
                $contactListMeetSearchCriteria = $searchCriteria['blacklist'] === (bool)$contactList['blacklist'];
            }
            if ($contactListMeetSearchCriteria) {
                $findContactList[] = ContactList::createFromArray($contactList);
            }
        }
        return $findContactList;
    }

    /**
     * @throws JsonException
     */
    public function save(ContactList $entity): ContactList
    {
        $this->loadData();

            $data = $this->contactListData;
            $itemIndex = $this->getItemIndex($entity);
            $item = $this->buildJsonDataForContactList($entity);
            $data[$itemIndex] = $item;
            $file = $this->pathToContactList;

        $jsonStr = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($file, $jsonStr);
        return $entity;
    }

    /** Получение индекса элемента с данными для списка контактов на основе id сущности
     * @param ContactList $entity
     * @return int
     */
    private function getItemIndex(ContactList $entity): int
    {
        $id = $entity->getIdEntry();
        $contactListIdToIndex = $this->contactListIdToIndex;
        if (false === array_key_exists($id, $contactListIdToIndex)) {
            throw new RuntimeException("Контакт с id '$id' не найден в хранилище");
        }
        return $contactListIdToIndex[$id];
    }

    /**
     * @param ContactList $entity
     * @return array
     */
    private function buildJsonDataForContactList(ContactList $entity): array
    {
        return [
            'id_entry' => $entity->getIdEntry(),
            'id_recipient' => $entity->getIdRecipient(),
            'blacklist' => $entity->isBlackList()
        ];
    }
}
