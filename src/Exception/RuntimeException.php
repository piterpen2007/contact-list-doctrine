<?php

namespace EfTech\ContactList\Exception;

use EfTech\ContactList\Infrastructure\Exception as BaseException;
/**
 * Исключение бросается в результате ошибок котоыре возникли во время выполнения
 */
class RuntimeException extends BaseException\RuntimeException implements ExceptionInterface
{

}