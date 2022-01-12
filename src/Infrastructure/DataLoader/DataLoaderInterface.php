<?php

namespace EfTech\ContactList\Infrastructure\DataLoader;

interface DataLoaderInterface
{
    /** Загружаю и десериализую данные
     * @param string $sourceName
     * @return array
     */
    public function loadData(string $sourceName): array;
}