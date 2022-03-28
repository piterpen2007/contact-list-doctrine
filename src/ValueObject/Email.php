<?php

namespace EfTech\ContactList\ValueObject;

use EfTech\ContactList\Exception\RuntimeException;

/**
 * Email
 */
class Email
{
    /**
     * Тип почты(пример - гугл)
     *
     * @var ?string
     */
    private ?string $typeEmail;
    /**
     * Сам адрес почты
     *
     * @var string
     */
    private string $email;

    /**
     * @param string $typeEmail Тип почты(пример - гугл)
     * @param string $email Сам адрес почты
     */
    public function __construct(string $typeEmail, string $email)
    {
        $this->validate($typeEmail, $email);
        $this->typeEmail = $typeEmail;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getTypeEmail(): string
    {
        return $this->typeEmail;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     *  Валидация данных для создания ValueObject
     *
     *
     * @param string $typeEmail
     * @param string $email
     */
    private function validate(string $typeEmail, string $email): void
    {
        if ('' === trim($email)) {
            throw new RuntimeException('Адрес почты не может быть пустой строкой');
        }
        if ('' === trim($typeEmail)) {
            throw new RuntimeException('Тип почты не может быть пустой строкой');
        }
        if (100 < strlen($email)) {
            throw new RuntimeException('Длина адреса почты не может превышать 100 символов');
        }
        if (50 < strlen($typeEmail)) {
            throw new RuntimeException('Длина типа почты не может превышать 50 символов');
        }
        if (1 !== preg_match('/^[a-zA-Z0-9]*@[a-zA-Z0-9]*[.]{1}[a-zA-Z0-9]*$/', $email)) {
            throw new RuntimeException('В email должен присутствовать символ @ и только одна точка');
        }
    }


}
