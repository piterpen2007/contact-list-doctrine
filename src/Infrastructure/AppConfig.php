<?php
namespace EfTech\ContactList\Infrastructure;
use EfTech\ContactList\Exception;
use EfTech\ContactList\Exception\ErrorCreateAppConfigException;

/**
 *  Конфиг приложения
 */
class AppConfig
{
    /** Скрывает сообщения о ошибках
     * @var bool
     */
    private bool $hideErrorMsg;

    /** Возвращает флаг, который указывает что нужно скрывать сообщения о ощибках
     * @return bool
     */
    public function isHideErrorMsg(): bool
    {
        return $this->hideErrorMsg;
    }

    /** Устанавливает флаг указывающий что нужно скрывать сообщение о ошибках
     * @param bool $hideErrorMsg
     */
    private function setHideErrorMsg(bool $hideErrorMsg): void
    {
        $this->hideErrorMsg = $hideErrorMsg;
    }
    /** Путь до файла с данными о получателях
     * @var string
     */
    private string $pathToRecipients = __DIR__ . '/../../data/recipient.json';
    /** Путь до файла с данными о родне
     * @var string
     */
    private string $pathToKinsfolk =__DIR__ . '/../../data/kinsfolk.json';
    /** Путь до файла с данными о клиентах
     * @var string
     */
    private string $pathToCustomers =__DIR__ . '/../../data/customers.json';
    /** Путь до файла с данными о коллегах
     * @var string
     */
    private string $pathToColleagues =__DIR__ . '/../../data/colleagues.json';
    /**
     * @var string Тип логера
     */
    private string $loggerType = 'nullLogger';

    /** Возвращает тип логера
     * @return string
     */
    public function getLoggerType(): string
    {
        return $this->loggerType;
    }

    /** Устанавливает тип логера
     * @param string $loggerType
     * @return AppConfig
     */
    private function setLoggerType(string $loggerType): AppConfig
    {
        $this->loggerType = $loggerType;
        return $this;
    }
    private string $pathToLogFile =  __DIR__ . '/../../var/log/app.log';

    /** Возвращает путь до файла с логами
     * @return string
     */
    public function getPathToLogFile(): string
    {
        return $this->pathToLogFile;
    }


    /** Устанавливает путь до файла логов
     *
     * @param string $pathToLogFile путь до файла с логами
     * @return AppConfig
     */
    private function setPathToLogFile(string $pathToLogFile): AppConfig
    {
        $this->validateFilePath($pathToLogFile);
        $this->pathToLogFile = $pathToLogFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathToRecipients(): string
    {
        return $this->pathToRecipients;
    }

    /**
     * @param string $pathToRecipients
     * @return AppConfig
     */
    private function setPathToRecipients(string $pathToRecipients): AppConfig
    {
        $this->validateFilePath($pathToRecipients);
        $this->pathToRecipients = $pathToRecipients;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathToKinsfolk(): string
    {
        return $this->pathToKinsfolk;
    }

    /**
     * @param string $pathToKinsfolk
     * @return AppConfig
     */
    private function setPathToKinsfolk(string $pathToKinsfolk): AppConfig
    {
        $this->validateFilePath($pathToKinsfolk);
        $this->pathToKinsfolk = $pathToKinsfolk;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathToCustomers(): string
    {
        return $this->pathToCustomers;
    }

    /**
     * @param string $pathToCustomers
     * @return AppConfig
     */
    private function setPathToCustomers(string $pathToCustomers): AppConfig
    {
        $this->validateFilePath($pathToCustomers);
        $this->pathToCustomers = $pathToCustomers;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathToColleagues(): string
    {
        return $this->pathToColleagues;
    }

    /**
     * @param string $pathToColleagues
     * @return AppConfig
     */
    private function setPathToColleagues(string $pathToColleagues): AppConfig
    {
        $this->validateFilePath($pathToColleagues);
        $this->pathToColleagues = $pathToColleagues;
        return $this;
    }

    /** Проверка на корректный путь
     *
     * @param string $path
     * @return void
     */
    private function validateFilePath(string $path):void
    {
        if(false === file_exists($path)) {
           throw new ErrorCreateAppConfigException('Некорректный путь до файла с данными');
        }
    }

    /**
     * @param array $config
     * @return static
     * @uses AppConfig::setPathToColleagues()
     * @uses AppConfig::setPathToCustomers()
     * @uses AppConfig::setPathToKinsfolk()
     * @uses AppConfig::setPathToRecipient()
     * @uses AppConfig::setLoggerType()
     * @uses AppConfig::setHideErrorMsg()
     */
    public static function createFromArray(array $config):self
    {
        $appConfig = new self();


        foreach ($config as $key => $value) {
            if(property_exists($appConfig, $key)) {
                $setter = 'set' . ucfirst($key);
                $appConfig->{$setter}($value);
            }
        }
        return $appConfig;
    }





}