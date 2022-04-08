<?php

namespace EfTech\ContactList\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use EfTech\BookLibrary\Entity\AbstractTextDocument;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Exception\RuntimeException;

/**
 * Email
 *
 *
 * @ORM\Table(name="email")
 * @ORM\Entity()
 */
class Email
{
    /**
     * Идентификатор почты
     *
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="email_id_seq")
     */
    private ?int $id = null;
    /**
     * Тип почты(пример - гугл)
     *
     * @var ?string
     * @ORM\Column(name="type_email", type="string",length=50, nullable=false)
     */
    private ?string $typeEmail;
    /**
     * Сам адрес почты
     *
     * @var string
     * @ORM\Column(name="email", type="string",length=100, nullable=false)
     */
    private string $email;


    /**
     * Получатель
     *
     * @var Recipient|null
     *
     * @ORM\ManyToOne(
     *     targetEntity=\EfTech\ContactList\Entity\Recipient::class,
     *     inversedBy="emails"
     *     )
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id_recipient")
     */
    private ?Recipient $recipient = null;



    /**
     * @param string $typeEmail Тип почты(пример - гугл)
     * @param string $email Сам адрес почты
     */
    public function __construct(string $typeEmail, string $email)
    {
        $this->validate($typeEmail, $email);
        $this->typeEmail = $typeEmail;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getTypeEmail(): string
    {
        return $this->typeEmail;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     *  Валидация данных для создания ValueObject
     *
     *
     * @param string $typeEmail
     * @param string $email
     */
    private function validate(string $typeEmail, string $email): void
    {
        if ('' === trim($email)) {
            throw new RuntimeException('Адрес почты не может быть пустой строкой');
        }
        if ('' === trim($typeEmail)) {
            throw new RuntimeException('Тип почты не может быть пустой строкой');
        }
        if (100 < strlen($email)) {
            throw new RuntimeException('Длина адреса почты не может превышать 100 символов');
        }
        if (50 < strlen($typeEmail)) {
            throw new RuntimeException('Длина типа почты не может превышать 50 символов');
        }
        if (1 !== preg_match('/^[a-zA-Z0-9]*@[a-zA-Z0-9]*[.]{1}[a-zA-Z0-9]*$/', $email)) {
            throw new RuntimeException('В email должен присутствовать символ @ и только одна точка');
        }
    }


}
