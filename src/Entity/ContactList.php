<?php

namespace EfTech\ContactList\Entity;

use Composer\Platform\Runtime;
use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\Exception\InvalidDataStructureException;
use EfTech\ContactList\Exception\RuntimeException;

/**
 * Класс описывающий Список контактов
 *
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\ContactListDoctrineRepository::class)
 * @ORM\Table(name="contact_list")
 */

class ContactList
{
    /**
     * ID получателя
     *
     * @ORM\OneToOne(targetEntity=\EfTech\ContactList\Entity\Recipient::class)
     * @ORM\JoinColumn(name="id_recipient", referencedColumnName="id_recipient")
     *
     * @var Recipient
     */

    private Recipient $recipient;

    /**
     * ID записи
     *
     * @ORM\Id
     * @ORM\Column(name="id_entry", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="contact_list_id_entry_seq")
     *
     * @var int
     */

    private int $id;
    /**
     * @var bool наличие в черном списке
     * * @ORM\Column(name="blacklist", type="boolean", nullable=false)
     *
     */
    private bool $blackList;

    /**
     * @param Recipient $recipient
     * @param int $id
     * @param bool $blackList
     */
    public function __construct(Recipient $recipient, int $id, bool $blackList)
    {
        $this->recipient = $recipient;
        $this->id = $id;
        $this->blackList = $blackList;
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isBlackList(): bool
    {
        return $this->blackList;
    }

    /** Перенос контакта в черный список
     * @return $this
     */
    public function moveToBlacklist(): self
    {
        if (true === $this->blackList) {
            throw new RuntimeException(
                "Контакт с id {$this->getRecipient()->getIdRecipient()} уже находится в черном списке"
            );
        }
        $this->blackList = true;
        return $this;
    }



    /**
     * @param array $data
     * @return ContactList
     */
    public static function createFromArray(array $data): ContactList
    {
        $requiredFields = [
            'id_recipient',
            'id_entry',
            'blacklist'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new invalidDataStructureException($errMsg);
        }

        return new ContactList(
            $data['id_recipient'],
            $data['id_entry'],
            $data['blacklist']
        );
    }
}
