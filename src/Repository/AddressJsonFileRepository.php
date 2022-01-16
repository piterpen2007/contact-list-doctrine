<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;
use EfTech\ContactList\Infrastructure\invalidDataStructureException;

class AddressJsonFileRepository implements AddressRepositoryInterface
{
    /** Текущее значение идентификатора адреса
     * @var int
     */
    private int $currentId;
    /**
     *
     *
     * @var string
     */
    private string $pathToAddress;
    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;
    /** данные о контактном списке
     * @var array|null
     */
    private ?array $addressData = null;

    /** Сопоставляет id адреса с номером элемента в $addressListData
     * @var array|null
     */
    private ?array $addressIdToIndex = null;

    /**
     * @param string $pathToAddress
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(string $pathToAddress, DataLoaderInterface $dataLoader)
    {
        $this->pathToAddress = $pathToAddress;
        $this->dataLoader = $dataLoader;
    }

    /**
     * @return array
     */
    private function loadData():array
    {
        if (null === $this->addressData) {
            $this->addressData = $this->dataLoader->loadData($this->pathToAddress);
            $this->addressIdToIndex = array_combine(
                array_map(
                    [$this,'extractTextDocument'],
                    $this->addressData
                ),
                array_keys($this->addressData)
            );
        }
        return $this->addressData;
    }

    private function extractTextDocument($v):int
    {
        if (false === is_array($v)) {
            throw new InvalidDataStructureException('Данные о текстовом документе должны быть массивом');
        }
        if (false === array_key_exists('id',$v)) {
            throw new InvalidDataStructureException('Нету id текстового документа');
        }
        if (false === is_int($v['id'])) {
            throw new InvalidDataStructureException('id текстового документа должен быть целым числом');
        }
        return $v['id'];

    }


    public function findBy(array $criteria): array
    {
        $addresses = $this->loadData();
        $findAddress = [];
        foreach ($addresses as $address) {
            if (array_key_exists('id_address',$criteria)) {
                $addressMeetSearchCriteria = $criteria['id_address'] === $address['id_address'];
            } else {
                $addressMeetSearchCriteria = true;
            }
            if ($addressMeetSearchCriteria && array_key_exists('id_recipient',$criteria)) {
                $addressMeetSearchCriteria = $criteria['id_recipient'] === $address['id_recipient'];
            }
            if ($addressMeetSearchCriteria && array_key_exists('address',$criteria)) {
                $addressMeetSearchCriteria = $criteria['address'] === $address['address'];
            }
            if ($addressMeetSearchCriteria && array_key_exists('status',$criteria)) {
                $addressMeetSearchCriteria = $criteria['status'] === $address['status'];
            }
            if ($addressMeetSearchCriteria) {
                $findAddress[] = Address::createFromArray($address);
            }
        }
        return $findAddress;
    }

    public function nextId(): int
    {
        $this->loadData();
        $this->currentId = $this->currentId + 1;
        return $this->currentId;

    }

    public function add(Address $entity): Address
    {
        $this->loadData();
        $item = $this->buildJsonDataAddress($entity);
        $this->addressData[] = $item;
        $data = $this->addressData;
        $this->addressIdToIndex[$entity->getIdAddress()] = array_key_last($this->addressData);
        $file = $this->pathToAddress;

        $jsonStr = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($file, $jsonStr);
        return $entity;
    }


    /**
     * @param Address $entity
     * @return array
     */
    private function buildJsonDataAddress(Address $entity): array
    {
        return [
            'id_address' => $entity->getIdAddress(),
            'id_recipient' => $entity->getIdRecipient(),
            'address' => $entity->getAddress(),
            'status' => $entity->getStatus()
        ];
    }
}