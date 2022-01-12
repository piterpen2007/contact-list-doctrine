<?php
namespace EfTech\ContactList\Entity;
use EfTech\ContactList\Exception;
use EfTech\ContactList\ValueObject\Balance;


final class Customer extends Recipient
{
    /**
     * @var string Контактный телефон клиента
     */
    private string $contractNumber;
    /**
     * @var int Средняя сумма по транзакциям клиента
     */
    private int $averageTransactionAmount;
    /**
     * @var string Скидка клиента
     */
    private string $discount;
    /**
     * @var string Время в которое можно беспокоить клиента
     */
    private string $timeToCall;

    /**
     * @param string $contractNumber
     * @param int $averageTransactionAmount
     * @param string $discount
     * @param string $timeToCall
     */
    public function __construct(int $id_recipient, string $full_name, string $birthday, string $profession,array $balance,string $contractNumber, int $averageTransactionAmount, string $discount, string $timeToCall)
    {
        parent::__construct($id_recipient, $full_name, $birthday, $profession, $balance);
        $this->contractNumber = $contractNumber;
        $this->averageTransactionAmount = $averageTransactionAmount;
        $this->discount = $discount;
        $this->timeToCall = $timeToCall;
    }


    /** Возвращает контактный телефон
     * @return string
     */
    final public function getContractNumber(): string
    {
        return $this->contractNumber;
    }

    /** Устанавливает контактный телефон
     * @param string $contractNumber
     * @return Customer
     */
    public function setContractNumber(string $contractNumber): Customer
    {
        $this->contractNumber = $contractNumber;
        return $this;
    }

    /** Возвращает среднюю сумму по операциям клиента
     * @return int
     */
    final public function getAverageTransactionAmount(): int
    {
        return $this->averageTransactionAmount;
    }

    /** Устанавливает среднюю сумму по операциям
     * @param int $averageTransactionAmount
     * @return Customer
     */
    public function setAverageTransactionAmount(int $averageTransactionAmount): Customer
    {
        $this->averageTransactionAmount = $averageTransactionAmount;
        return $this;
    }

    /** Возвращает скидку по клиенту
     * @return string
     */
    final public function getDiscount(): string
    {
        return $this->discount;
    }

    /** Устанавливает скидку
     * @param string $discount
     * @return Customer
     */
    public function setDiscount(string $discount): Customer
    {
        $this->discount = $discount;
        return $this;
    }

    /** Возвращает время беспокоиства
     * @return string
     */
    final public function getTimeToCall(): string
    {
        return $this->timeToCall;
    }

    /** Устанавливает время беспокоиства
     * @param string $time_to_call
     * @return Customer
     */
    public function setTimeToCall(string $time_to_call): Customer
    {
        $this->timeToCall = $time_to_call;
        return $this;
    }
    public static function createFromArray(array $data):Customer
    {

        $requiredFields = [
            'id_recipient',
            'full_name',
            'birthday',
            'profession',
            'balance',
            'contract_number',
            'average_transaction_amount',
            'discount',
            'time_to_call'
        ];

        $missingFields = array_diff($requiredFields,array_keys($data));

        if (count($missingFields) > 0) {
            $errMsg = sprintf('Отсутствуют обязательные элементы: %s', implode(',', $missingFields));
            throw new Exception\invalidDataStructureException($errMsg);
        }
        return new Customer(
            $data['id_recipient'],
            $data['full_name'],
            $data['birthday'],
            $data['profession'],
            $data['balance'],
            $data['contract_number'],
            $data['average_transaction_amount'],
            $data['discount'],
            $data['time_to_call']
        );
    }

}