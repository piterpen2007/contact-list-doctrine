<?php

namespace EfTech\ContactList\Service\SearchContactListService;

class SearchContactListCriteria
{
    private ?int $idRecipient;
    private ?int $idEntry;
    private ?bool $blacklist;

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @param int|null $idRecipient
     * @return SearchContactListCriteria
     */
    public function setIdRecipient(?int $idRecipient): SearchContactListCriteria
    {
        $this->idRecipient = $idRecipient;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdEntry(): ?int
    {
        return $this->idEntry;
    }

    /**
     * @param int|null $idEntry
     * @return SearchContactListCriteria
     */
    public function setIdEntry(?int $idEntry): SearchContactListCriteria
    {
        $this->idEntry = $idEntry;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getBlacklist(): ?bool
    {
        return $this->blacklist;
    }

    /**
     * @param bool|null $blacklist
     * @return SearchContactListCriteria
     */
    public function setBlacklist(?bool $blacklist): SearchContactListCriteria
    {
        $this->blacklist = $blacklist;
        return $this;
    }
}
