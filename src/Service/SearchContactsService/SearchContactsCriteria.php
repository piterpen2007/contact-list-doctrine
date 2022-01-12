<?php

namespace EfTech\ContactList\Service\SearchContactsService;

class SearchContactsCriteria
{
    private ?string $category;

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string|null $category
     * @return SearchContactsCriteria
     */
    public function setCategory(?string $category): SearchContactsCriteria
    {
        $this->category = $category;
        return $this;
    }

}