<?php

namespace EfTech\ContactList\Service\SearchColleagueService;

class SearchColleagueCriteria
{
    private ?int $idRecipient = null;
    private ?string $fullName = null;
    private ?string $birthday = null;
    private ?string $profession = null;
    private ?string $department = null;
    private ?string $position = null;
    private ?string $room_number = null;

    /**
     * @return int|null
     */
    public function getIdRecipient(): ?int
    {
        return $this->idRecipient;
    }

    /**
     * @param int|null $idRecipient
     * @return SearchColleagueCriteria
     */
    public function setIdRecipient(?int $idRecipient): SearchColleagueCriteria
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
     * @return SearchColleagueCriteria
     */
    public function setFullName(?string $fullName): SearchColleagueCriteria
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
     * @return SearchColleagueCriteria
     */
    public function setBirthday(?string $birthday): SearchColleagueCriteria
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
     * @return SearchColleagueCriteria
     */
    public function setProfession(?string $profession): SearchColleagueCriteria
    {
        $this->profession = $profession;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @param string|null $department
     * @return SearchColleagueCriteria
     */
    public function setDepartment(?string $department): SearchColleagueCriteria
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     * @return SearchColleagueCriteria
     */
    public function setPosition(?string $position): SearchColleagueCriteria
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoomNumber(): ?string
    {
        return $this->room_number;
    }

    /**
     * @param string|null $room_number
     * @return SearchColleagueCriteria
     */
    public function setRoomNumber(?string $room_number): SearchColleagueCriteria
    {
        $this->room_number = $room_number;
        return $this;
    }

}