<?php

namespace EfTech\ContactList\Service\SearchKinsfolkService;

class SearchKinsfolkCriteria
{
    private ?int $idRecipient = null;
    private ?string $fullName = null;
    private ?string $birthday = null;
    private ?string $profession = null;
    private ?string $status = null;
    private ?string $ringtone = null;
    private ?string $hotkey = null;

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return SearchKinsfolkCriteria
     */
    public function setStatus(?string $status): SearchKinsfolkCriteria
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRingtone(): ?string
    {
        return $this->ringtone;
    }

    /**
     * @param string|null $ringtone
     * @return SearchKinsfolkCriteria
     */
    public function setRingtone(?string $ringtone): SearchKinsfolkCriteria
    {
        $this->ringtone = $ringtone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHotkey(): ?string
    {
        return $this->hotkey;
    }

    /**
     * @param string|null $hotkey
     * @return SearchKinsfolkCriteria
     */
    public function setHotkey(?string $hotkey): SearchKinsfolkCriteria
    {
        $this->hotkey = $hotkey;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @param int|null $idRecipient
     * @return SearchKinsfolkCriteria
     */
    public function setIdRecipient(?int $idRecipient): SearchKinsfolkCriteria
    {
        $this->idRecipient = $idRecipient;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return SearchKinsfolkCriteria
     */
    public function setFullName(?string $fullName): SearchKinsfolkCriteria
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * @param string|null $birthday
     * @return SearchKinsfolkCriteria
     */
    public function setBirthday(?string $birthday): SearchKinsfolkCriteria
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfession(): ?string
    {
        return $this->profession;
    }

    /**
     * @param string|null $profession
     * @return SearchKinsfolkCriteria
     */
    public function setProfession(?string $profession): SearchKinsfolkCriteria
    {
        $this->profession = $profession;
        return $this;
    }
}
