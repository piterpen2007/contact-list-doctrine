<?php
namespace EfTech\ContactList\Infrastructure\DataLoader;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;

class JsonDataLoader implements DataLoaderInterface
{
    /** Загрузка данных из ресурса
     * @param string $sourceName
     * @return array
     * @throws \JsonException
     */
    function loadData (string $sourceName):array
    {
        $content = file_get_contents($sourceName);
        return json_decode($content, true,512 , JSON_THROW_ON_ERROR);
    }

}