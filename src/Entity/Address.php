<?php

namespace EfTech\ContactList\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\Entity\Address\Status;
use EfTech\ContactList\Exception\InvalidDataStructureException;

/**
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\AddressDoctrineRepository::class)
 * @ORM\Table(
 *     name="address",
 *     indexes={
 *          @ORM\Index(name="address_address_idx", columns={"address"})
 *     }
 * )
 */
class Address
{
    /**
     * ID адреса
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="address_id_address_seq")
     * @ORM\Column(type="integer", name="id_address", nullable=false)
     *
     * @var int
     */
    private int $idAddress;
    /**
     * Массив контактов адреса
     *
     * @ORM\ManyToMany(targetEntity=\EfTech\ContactList\Entity\Recipient::class)
     * @ORM\JoinTable(
     *     name="address_to_recipients",
     *     joinColumns={@ORM\JoinColumn(name="id_address", referencedColumnName="id_address")},
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="id_recipient", referencedColumnName="id_recipient", onDelete="CASCADE")
     * }
     * )
     *
     * @var Recipient[]|Collection
     */
    private Collection $recipients;
    /**
     * @var string адрес
     * @ORM\Column (name="address", type="string", length=255, nullable=false)
     */
    private string $address;

    /**
     * статус адреса (дом\работа)
     *
     * @ORM\ManyToOne(targetEntity=\EfTech\ContactList\Entity\Address\Status::class, cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")

     * @var Status
     */
    private Status $status;

    /**
     * @param int $idAddress
     * @param array $recipients
     * @param string $address
     * @param Status $status
     */
    public function __construct(int $idAddress, array $recipients, string $address, Status $status)
    {
        $this->idAddress = $idAddress;
        $this->recipients = new ArrayCollection($recipients);
        $this->address = $address;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getIdAddress(): int
    {
        return $this->idAddress;
    }

    /**
     * @return Collection|Recipient[]
     */
    public function getRecipients()
    {
        return $this->recipients->toArray();
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param array $data
     * @return Address
     */
    public static function createFromArray(array $data): Address
    {
        $requiredFields = [
            'id_address',
            'id_recipient',
            'address',
            'status'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new InvalidDataStructureException($errMsg);
        }

        return new Address(
            $data['id_address'],
            $data['id_recipient'],
            $data['address'],
            $data['status']
        );
    }
}
