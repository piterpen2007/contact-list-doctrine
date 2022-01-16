<?php

namespace EfTech\ContactList\Service;

use EfTech\ContactList\Entity\ContactList;
use EfTech\ContactList\Entity\ContactListRepositoryInterface;
use EfTech\ContactList\Service\MoveToBlacklistService\Exception\ContactListNotFoundException;
use EfTech\ContactList\Service\MoveToBlacklistService\MoveToBlacklistDto;

class MoveToBlacklistContactListService
{

    /** Репозиторий для работы с текстовыми документами
     * @var ContactListRepositoryInterface
     */
    private ContactListRepositoryInterface $contactListRepository;

    /**
     * @param ContactListRepositoryInterface $contactListRepository
     */
    public function __construct(ContactListRepositoryInterface $contactListRepository)
    {
        $this->contactListRepository = $contactListRepository;
    }

    /** Добавляет контакт в черный список с заданным id
     * @param int $contactListId - id записи
     * @return MoveToBlacklistDto
     */
    public function move(int $contactListId): MoveToBlacklistDto
    {
        $entities = $this->contactListRepository->findBy(['id_recipient' => $contactListId]);
        if(1 !== count($entities)) {
            throw new ContactListNotFoundException(
                "Не удалось отправить контакт в чёрный список. Запись с id='$contactListId' не найден."
            );
        }
        /** @var $entity ContactList */
        $entity = current($entities);
        $entity->moveToBlacklist();

        $this->contactListRepository->save($entity);

        return new MoveToBlacklistDto($entity->isBlackList());
    }
}