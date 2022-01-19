<?php

namespace EfTech\ContactList\Repository\UserRepository;

use EfTech\ContactList\Entity\User;
use EfTech\ContactList\Infrastructure\Auth\UserDataProviderInterface;

/**
 * Поставщик данных о логине\пароле пользователя
 */
class UserDataProvider extends User implements
    UserDataProviderInterface
{

}