<?php

namespace EfTech\ContactList\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Класс, реализующий логику создания пользователя
 *
 * @ORM\MappedSuperclass()
 */
class User
{
    /**
     * Идентификатор пользователя
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="users_id_seq")
     */
    private int $id;

    /** Логин пользователя в системе
     * @var string
     * @ORM\Column(name="login", type="string", nullable=false, length=50)
     */
    private string $login;
    /** Пароль пользователя
     *
     *
     * @ORM\Column(name="password", type="string", nullable=false, length=100)
     * @var string
     */
    private string $password;

    /**
     * @param int $id id пользователя
     * @param string $login Логин пользователя в системе
     * @param string $password Пароль пользователя
     */
    public function __construct(int $id, string $login, string $password)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
