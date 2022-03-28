<?php

namespace EfTech\ContactList\Service\SearchRecipientsService;

use DateTimeImmutable;
use EfTech\ContactList\ValueObject\Email;

/**
 *  Структура информации о получателях
 */
class RecipientDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected DateTimeImmutable $birthday;
    protected string $profession;
    protected array $emails;


    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param array $emails
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        DateTimeImmutable $birthday,
        string $profession,
        array $emails
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->emails = $emails;
    }

    /**
     * @return int
     */
    public function getIdRecipient(): int
    {
        return $this->id_recipient;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getProfession(): string
    {
        return $this->profession;
    }

    /**
     * @return Email[]
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

}
