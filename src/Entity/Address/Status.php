<?php

namespace EfTech\ContactList\Entity\Address;

use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\Exception;

/**
 * Статус адреса
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="address_status"
 *
 * )
 */

final class Status
{
    /**
     * Теневой id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="address_status_id_seq")
     */
    private int $id = -1;

    /**
     * Домашний статус адреса
     */
    public const STATUS_HOME = 'Home';

    /**
     * Рабочий статус адреса
     */
    public const STATUS_WORK = 'Work';

    /**
     * Допустимые статусы
     */
    private const ALLOWED_STATUS = [
        self::STATUS_WORK => self::STATUS_WORK,
        self::STATUS_HOME  => self::STATUS_HOME,
    ];

    /**
     * Статус
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     *
     * @var string
     */
    private string $name;

    /**
     * @param string $name - Название статуса
     */
    public function __construct(string $name)
    {
        $this->validate($name);
        $this->name = $name;
    }

    /**
     * Валидация статуса
     *
     * @param string $name - Название статуса
     *
     * @return void
     */
    private function validate(string $name): void
    {
        if (false === array_key_exists($name, self::ALLOWED_STATUS)) {
            throw new Exception\RuntimeException('Некорректный статус текстового документа');
        }
    }

    /**
     * Возвращает наименование статуса
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Каст к string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
