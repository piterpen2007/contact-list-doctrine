<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Service\SearchAddressService\AddressDto;
use EfTech\ContactList\Service\SearchAddressService\SearchAddressCriteria;

class SearchAddressService
{
    /**
     *
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /** Репозиторий адресов
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @param LoggerInterface $logger
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(LoggerInterface $logger, AddressRepositoryInterface $addressRepository)
    {
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
    }
    /**
     * @param Address $address
     * @return AddressDto
     */
    private function createDto(Address $address): AddressDto
    {
        return new AddressDto(
            $address->getIdAddress(),
            $address->getIdRecipient(),
            $address->getAddress(),
            $address->getStatus()
        );
    }

    /**
     *
     *
     * @param SearchAddressCriteria $addressCriteria
     * @return AddressDto[]
     */
    public function search(SearchAddressCriteria $addressCriteria): array
    {
        $criteria = $this->searchCriteriaToArray($addressCriteria);
        $entitiesCollection = $this->addressRepository->findBy($criteria);
        $dtoCollection = [];
        foreach ($entitiesCollection as $entity) {
            $dtoCollection[] = $this->createDto($entity);
        }
        $this->logger->debug("Найдено addresses: " . count($entitiesCollection));
        return $dtoCollection;
    }

    /** Преобразует критерии поиска в массив
     * @param SearchAddressCriteria $addressCriteria
     * @return array
     */
    private function searchCriteriaToArray(SearchAddressCriteria $addressCriteria): array
    {
        $criteriaForRepository = [
            'id_address' => $addressCriteria->getIdAddress(),
            'id_recipient' => $addressCriteria->getIdRecipient(),
            'address' => $addressCriteria->getAddress(),
            'status' => $addressCriteria->getStatus()
        ];
        return array_filter($criteriaForRepository, static function ($v): bool {
            return null !== $v;
        });
    }
}
