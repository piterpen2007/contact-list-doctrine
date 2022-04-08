<?php

namespace EfTech\ContactList\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use EfTech\ContactList\Exception;

/**
 * Родственники
 *
 * @ORM\Entity(repositoryClass=\EfTech\ContactList\Repository\KinsfolkDoctrineRepository::class)
 * @ORM\Table(
 *     name="kinsfolk",
 *     indexes={
 *          @ORM\Index(name="kinsfolk_status_idx", columns={"status"}),
 *     }
 * )
 */
final class Kinsfolk extends Recipient
{
    /**
     * @var string Статус родственника
     * @ORM\Column(type="string",name="status", length=50, nullable=false)
     */
    private string $status;
    /**
     * @var string Рингтон стоящий на родственнике
     * @ORM\Column(type="string",name="ringtone", length=50, nullable=false)
     */
    private string $ringtone;
    /**
     * @var string горячая клавиша родственника
     * @ORM\Column(type="string",name="hotkey", length=3, nullable=false)
     */
    private string $hotkey;

    /**
     * @param int $id_recipient
     * @param string $full_name
     * @param DateTimeImmutable $birthday
     * @param string $profession
     * @param array $emails
     * @param string $status
     * @param string $ringtone
     * @param string $hotkey
     */
    public function __construct(
        int $id_recipient,
        string $full_name,
        DateTimeImmutable $birthday,
        string $profession,
        array $emails,
        string $status,
        string $ringtone,
        string $hotkey
    ) {
        parent::__construct($id_recipient, $full_name, $birthday, $profession, $emails);
        $this->status = $status;
        $this->ringtone = $ringtone;
        $this->hotkey = $hotkey;
    }


    /** Возвращает статус родственника
     * @return string
     */
    final public function getStatus(): string
    {
        return $this->status;
    }

    /** Устанавливает статус родственника
     * @param string $status
     * @return Kinsfolk
     */
    public function setStatus(string $status): Kinsfolk
    {
        $this->status = $status;
        return $this;
    }

    /** Возвращает рингтон родственника
     * @return string
     */
    final public function getRingtone(): string
    {
        return $this->ringtone;
    }

    /** Устанавливает рингтон
     * @param string $ringtone
     * @return Kinsfolk
     */
    final public function setRingtone(string $ringtone): Kinsfolk
    {
        $this->ringtone = $ringtone;
        return $this;
    }

    /** Возвращает горячую клавишу
     * @return string
     */
    final public function getHotkey(): string
    {
        return $this->hotkey;
    }

    /** Устанавливает горячую клавишу
     * @param string $hotkey
     * @return Kinsfolk
     */
    public function setHotkey(string $hotkey): Kinsfolk
    {
        $this->hotkey = $hotkey;
        return $this;
    }

    public static function createFromArray(array $data): Kinsfolk
    {

        $requiredFields = [
            'id_recipient',
            'full_name',
            'birthday',
            'profession',
            'balance',
            'status',
            'ringtone',
            'hotkey'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new Exception\invalidDataStructureException($errMsg);
        }
        return new Kinsfolk(
            $data['id_recipient'],
            $data['full_name'],
            $data['birthday'],
            $data['profession'],
            $data['balance'],
            $data['status'],
            $data['ringtone'],
            $data['hotkey']
        );
    }
}
