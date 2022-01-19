<?php

namespace EfTech\ContactList\Entity;

/**
 * Пользователь системы
 */
class User
{
    /** id пользователя
     * @var int
     */
    private int $id;

    /** Логин пользователя в системе
     * @var string
     */
    private string $login;
    /** Пароль пользователя
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