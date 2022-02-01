<?php

namespace EfTech\ContactList\Service\SearchContactListService;

class ContactListDto
{
    /**
     * @var int|null
     */
    private ?int $idRecipient;
    /**
     * @var int|null
     */
    private ?int $idEntry;
    /**
     * @var bool|null
     */
    private ?bool $blacklist;

    /**
     * @param int|null $idRecipient
     * @param int|null $idEntry
     * @param string|null $blacklist
     */
    public function __construct(?int $idRecipient, ?int $idEntry, ?string $blacklist)
    {
        $this->idRecipient = $idRecipient;
        $this->idEntry = $idEntry;
        $this->blacklist = $blacklist;
    }

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @return int|null
     */
    public function getIdEntry(): ?int
    {
        return $this->idEntry;
    }

    /**
     * @return bool|null
     */
    public function getBlacklist(): ?bool
    {
        return $this->blacklist;
    }
}
