<?php

namespace EfTech\ContactList\Repository\UserRepository;

use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\Entity\User;
use EfTech\ContactList\Infrastructure\Auth\UserDataProviderInterface;

/**
 * Поставщик данных о логине/пароле пользователя
 *
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\UserDoctrineRepository::class)
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="users_login_unq", columns={"login"})
 *     }
 * )
 */
class UserDataProvider extends User implements
    UserDataProviderInterface
{
}
