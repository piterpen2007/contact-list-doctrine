<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\Address\Status;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Service\ArrivalNewAddressService\NewAddressDto;
use EfTech\ContactList\Service\ArrivalNewAddressService\ResultRegisterNewAddressDto;

class ArrivalAddressService
{
    /** Репозиторий для работы с адресами
     *
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;


    /**
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository
    ) {
        $this->addressRepository = $addressRepository;
    }

    public function registerAddress(NewAddressDto $addressDto): ResultRegisterNewAddressDto
    {
//        $recipientId = $addressDto->getIdRecipient();
//
//        $contactsCollections = $this->addressRepository->findBy(['id_recipient' => $recipientId]);
//
//        if(count($contactsCollections) >= 1) {
//            throw new RuntimeException(
//                'Нельзя зарегистрировать адрес с id_recipient = ' . $recipientId .
// '. Контакт с данным id  уже имеет адрес.'
//            );
//        }

        $entity = new Address(
            $this->addressRepository->nextId(),
            $addressDto->getIdRecipient(),
            $addressDto->getAddress(),
            new Status($addressDto->getStatus())
        );

        $this->addressRepository->add($entity);


        return new ResultRegisterNewAddressDto(
            $entity->getIdAddress(),
            $entity->getIdRecipient(),
            $entity->getAddress(),
            $entity->getStatus()->getName()
        );
    }
}
