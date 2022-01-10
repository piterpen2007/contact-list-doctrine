<?php

namespace EfTech\ContactList\Service\SearchRecipientsService;

class SearchRecipientsCriteria
{
    private ?int $idRecipient;
    private ?string $fullName;
    private ?string $birthday;
    private ?string $profession;

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @param int|null $idRecipient
     * @return SearchRecipientsCriteria
     */
    public function setIdRecipient(?int $idRecipient): SearchRecipientsCriteria
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
     * @return SearchRecipientsCriteria
     */
    public function setFullName(?string $fullName): SearchRecipientsCriteria
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
     * @return SearchRecipientsCriteria
     */
    public function setBirthday(?string $birthday): SearchRecipientsCriteria
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
     * @return SearchRecipientsCriteria
     */
    public function setProfession(?string $profession): SearchRecipientsCriteria
    {
        $this->profession = $profession;
        return $this;
    }

}