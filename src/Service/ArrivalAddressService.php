<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\Address\Status;
use EfTech\ContactList\Entity\AddressRepositoryInterface;
use EfTech\ContactList\Entity\RecipientRepositoryInterface;
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
     * Репозиторий для работы с контактами
     *
     * @var RecipientRepositoryInterface
     */
    private RecipientRepositoryInterface $recipientRepository;


    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param RecipientRepositoryInterface $recipientRepository
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        \EfTech\ContactList\Entity\RecipientRepositoryInterface $recipientRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->recipientRepository = $recipientRepository;
    }

    public function registerAddress(NewAddressDto $addressDto): ResultRegisterNewAddressDto
    {
//        $recipientData = [];
//        foreach ($addressDto->getIdRecipient() as $idRecipient) {
            $recipientData = $this->loadContactEntities($addressDto->getIdRecipient());
//        }

        $entity = new Address(
            $this->addressRepository->nextId(),
            $recipientData,
            $addressDto->getAddress(),
            new Status($addressDto->getStatus())
        );

        $this->addressRepository->add($entity);


        return new ResultRegisterNewAddressDto(
            $entity->getIdAddress(),
            $entity->getRecipients(),
            $entity->getAddress(),
            $entity->getStatus()->getName()
        );
    }

    /**
     * Загрузка сущностей контактов по их id
     *
     * @param array $idRecipient
     *
     * @return array
     */
    private function loadContactEntities(array $idRecipient): array
    {
        return $this->recipientRepository->findBy(['id_recipient_list' => $idRecipient]);
    }
}
