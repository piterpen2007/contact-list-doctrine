<?php

namespace EfTech\ContactList\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\ValueObject\Balance;
use EfTech\ContactList\Exception;
use EfTech\ContactList\ValueObject\Email;

/**
 * Коллеги
 *
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\ColleagueDoctrineRepository::class)
 * @ORM\Table(
 *     name="colleagues",
 *     indexes={
 *          @ORM\Index(name="colleagues_position_idx", columns={"position"}),
 *     }
 * )
 */
final class Colleague extends Recipient
{
    /**
     * @var string Отдел коллеги
     * @ORM\Column(type="string",name="department", length=50, nullable=false)
     */
    private string $department;
    /**
     * @var string Должность коллеги
     * @ORM\Column(type="string",name="position", length=50, nullable=false)
     */
    private string $position;
    /**
     * @var string Номер кабинета
     * @ORM\Column(type="string",name="room_number", length=3, nullable=false)
     */
    private string $roomNumber;

    /**
     * @param int $id_recipient
     * @param string $full_name
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param array $emails
     * @param string $department
     * @param string $position
     * @param string $roomNumber
     */
    public function __construct(
        int $id_recipient,
        string $full_name,
        DateTimeImmutable $birthday,
        string $profession,
        array $emails,
        string $department,
        string $position,
        string $roomNumber
    ) {
        parent::__construct($id_recipient, $full_name, $birthday, $profession, $emails);
        $this->department = $department;
        $this->position = $position;
        $this->roomNumber = $roomNumber;
    }


    /**
     * @return string Возвращает отдел
     */
    final public function getDepartment(): string
    {
        return $this->department;
    }


    /** Возвращает должность
     * @return string
     */
    final public function getPosition(): string
    {
        return $this->position;
    }


    /** Возвращает номер кабинета
     * @return string
     */
    final public function getRoomNumber(): string
    {
        return $this->roomNumber;
    }

    public static function createFromArray(array $data): Colleague
    {

        $requiredFields = [
            'id_recipient',
            'full_name',
            'birthday',
            'profession',
            'balance',
            'department',
            'position',
            'room_number'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new Exception\invalidDataStructureException($errMsg);
        }
        return new Colleague(
            $data['id_recipient'],
            $data['full_name'],
            $data['birthday'],
            $data['profession'],
            $data['balance'],
            $data['department'],
            $data['position'],
            $data['room_number']
        );
    }
}
