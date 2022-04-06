<?php

namespace EfTech\ContactList\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use EfTech\ContactList\Exception;
use EfTech\ContactList\ValueObject\Email;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\RecipientDoctrineRepository::class)
 * @ORM\Table(
 *     name="recipients",
 *     indexes={
 *          @ORM\Index(name="recipients_full_name_index", columns={"full_name"}),
 *          @ORM\Index(name="recipients_profession_index", columns={"profession"})
 *     }
 * )
 */
class Recipient
{
    /** Email контакта
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity=\EfTech\ContactList\ValueObject\Email::class,
     *     mappedBy="recipient"
     * )
     */
    private Collection $emails;
    /**
     * @var int id Получателя
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="recipients_id_recipient_seq")
     * @ORM\Column(type="integer",name="id_recipient",nullable=false)
     */
    private int $id_recipient;
    /**
     * @var string Полное имя получателя
     * @ORM\Column(type="string",name="full_name", length=100, nullable=false)
     */
    private string $full_name;
    /**
     * @var DateTimeImmutable Дата рождения получателя
     * @ORM\Column(type="datetime_immutable",name="birthday",nullable=false)
     */
    private DateTimeImmutable $birthday;
    /**
     *
     * @var string Профессия получателя
     *
     * @ORM\Column(type="string",name="profession", length=60, nullable=false)
     */
    private string $profession;

    /** Конструктор класса
     * @param int $id_recipient
     * @param string $full_name
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param array $emails
     */
    public function __construct(
        int $id_recipient,
        string $full_name,
        DateTimeImmutable $birthday,
        string $profession,
        array $emails
    ) {
        $this->id_recipient = $id_recipient;
        $this->full_name = $full_name;
        $this->birthday = $birthday;
        $this->profession = $profession;
        foreach ($emails as $email) {
            if (!$email instanceof Email) {
                throw new DomainException('Некорректный формат данных по закупочной цене');
            }
        }
        $this->emails = new ArrayCollection($emails);
    }

    /** Возвращает данные о почте
     * @return Email[]
     */
    public function getEmails(): array
    {
        return $this->emails->toArray();
    }

    /**
     * @return int Возвращает id получателя
     */
    final public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /** Устанавливает id получателя
     * @param int $id_recipient
     * @return Recipient
     */
    public function setIdRecipient(int $id_recipient): Recipient
    {
        $this->id_recipient = $id_recipient;
        return $this;
    }

    /** Возвращает полное имя получателя
     * @return string
     */
    final public function getFullName(): string
    {
        return $this->full_name;
    }

    /** Устанавливает полное имя получателя
     * @param string $full_name
     * @return Recipient
     */
    public function setFullName(string $full_name): Recipient
    {
        $this->full_name = $full_name;
        return $this;
    }

    /** Возвращает дату рождения получателя
     * @return DateTimeImmutable
     */
    final public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }

    /** Устанавливает дату рождения получателя
     * @param DateTimeImmutable $birthday
     * @return Recipient
     */
    public function setBirthday(DateTimeImmutable $birthday): Recipient
    {
        $this->birthday = $birthday;
        return $this;
    }

    /** Возвращает профессию получателя
     * @return string
     */
    final public function getProfession(): string
    {
        return $this->profession;
    }

    /** Устанавливает профессию получателя
     * @param string $profession
     * @return Recipient
     */
    public function setProfession(string $profession): Recipient
    {
        $this->profession = $profession;
        return $this;
    }

    /**
     * @param array $data
     * @return Recipient
     */
    public static function createFromArray(array $data): Recipient
    {
        $requiredFields = [
            'id_recipient',
            'full_name',
            'birthday',
            'profession',
            'balance'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new Exception\InvalidDataStructureException($errMsg);
        }

        return new Recipient(
            $data['id_recipient'],
            $data['full_name'],
            $data['birthday'],
            $data['profession'],
            $data['balance']
        );
    }
}
