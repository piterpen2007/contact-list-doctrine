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
    /** данные о адресах
     * @var array|null
     */
    private ?array $addressData = null;


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
            $this->currentId = max(
                array_map(
                    static function (array $v) {return $v['id_address'];},
                    $this->addressData
                )
            );
        }
        return $this->addressData;
    }

    private function extractTextDocument($v):int
    {
        if (false === is_array($v)) {
            throw new InvalidDataStructureException('Данные о адресе должны быть массивом');
        }
        if (false === array_key_exists('id_address',$v)) {
            throw new InvalidDataStructureException('Нету id адреса');
        }
        if (false === is_int($v['id_address'])) {
            throw new InvalidDataStructureException('id адреса должен быть целым числом');
        }
        return $v['id_address'];

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
        ++$this->currentId;
        return $this->currentId;

    }

    public function add(Address $entity): Address
    {
        $this->loadData();
        $item = $this->buildJsonDataAddress($entity);
        $this->addressData[] = $item;
        $data = $this->addressData;
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