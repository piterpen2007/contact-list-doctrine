<?php

namespace EfTech\ContactList\Service\SearchKinsfolkService;

use DateTimeImmutable;
use EfTech\ContactList\ValueObject\Balance;

/**
 *  Структура информации о получателях
 */
class KinsfolkDto
{
    protected int $id_recipient;
    protected string $fullName;
    protected DateTimeImmutable $birthday;
    protected string $profession;
    protected string $status;
    protected string $ringtone;
    protected string $hotkey;
    protected array $emails;

    /**
     * @param int $id_recipient
     * @param string $fullName
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param string $status
     * @param string $ringtone
     * @param string $hotkey
     * @param array $emails
     */
    public function __construct(
        int $id_recipient,
        string $fullName,
        DateTimeImmutable $birthday,
        string $profession,
        string $status,
        string $ringtone,
        string $hotkey,
        array $emails
    ) {
        $this->id_recipient = $id_recipient;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->profession = $profession;
        $this->status = $status;
        $this->ringtone = $ringtone;
        $this->hotkey = $hotkey;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRingtone(): string
    {
        return $this->ringtone;
    }

    /**
     * @return string
     */
    public function getHotkey(): string
    {
        return $this->hotkey;
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }
}
