<?php
namespace EfTech\ContactList\Entity;
use EfTech\ContactList\Exception;
use EfTech\ContactList\ValueObject\Balance;

final class Kinsfolk extends Recipient
{
    /**
     * @var string Статус родственника
     */
    private string $status;
    /**
     * @var string Рингтон стоящий на родственнике
     */
    private string $ringtone;
    /**
     * @var string горячая клавиша родственника
     */
    private string $hotkey;

    /**
     * @param string $status
     * @param string $ringtone
     * @param string $hotkey
     */
    public function __construct(int $id_recipient, string $full_name, string $birthday, string $profession, array $balance, string $status, string $ringtone, string $hotkey)
    {
        parent::__construct($id_recipient, $full_name, $birthday, $profession, $balance);
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

    public static function createFromArray(array $data):Kinsfolk
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

        $missingFields = array_diff($requiredFields,array_keys($data));

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