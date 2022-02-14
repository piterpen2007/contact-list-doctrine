<?php

namespace EfTech\ContactList\Config;

use EfTech\ContactList\Infrastructure\Logger\SymfonyDi\DiLoggerExt;
use EfTech\ContactList\Infrastructure\Router\SymfonyDi\DiRouterExt;
use EfTech\ContactList\Infrastructure\ViewTemplate\SymfonyDi\DiViewTemplateExt;

final class ContainerExtensions
{
    /** Возвращает коллекцию расширений di контейнера симфони для работу http риложения
     * @return mixed
     */
    public static function httpAppContainerExtension(): array
    {
        return [
            new DiRouterExt(),
            new DiLoggerExt(),
            new DiViewTemplateExt()
        ];
    }

    /** Возвращает коллекцию расширений di контейнера симфони для работы консольного приложения
     * @return array
     */
    public static function consoleContainerExtension(): array
    {
        return [
            new DiRouterExt(),
            new DiLoggerExt()
        ];
    }
}