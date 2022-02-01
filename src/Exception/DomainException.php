<?php

namespace EfTech\ContactList\Exception;

use EfTech\ContactList\Infrastructure\Exception as BaseException;

/**
 * Выбрасывает исключение, если значеине ге соответствует определенной допустимой области данных
 */
class DomainException extends BaseException\DomainException implements ExceptionInterface
{
}
