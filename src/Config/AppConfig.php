<?php

namespace EfTech\ContactList\Config;

use EfTech\ContactList\Exception\ErrorCreateAppConfigException;
use EfTech\ContactList\Infrastructure\HttpApplication\AppConfig as BaseConfig;

/**
 *  Конфиг приложения
 */
class AppConfig extends BaseConfig
{
    /** Путь до файла с данными о получателях
     * @var string
     */
    private string $pathToRecipients = __DIR__ . '/../../data/recipient.json';
    /** Путь до файла с данными о родне
     * @var string
     */
    private string $pathToKinsfolk = __DIR__ . '/../../data/kinsfolk.json';
    /** Путь до файла с данными о клиентах
     * @var string
     */
    private string $pathToCustomers = __DIR__ . '/../../data/customers.json';
    /** Путь до файла с данными о коллегах
     * @var string
     */
    private string $pathToColleagues = __DIR__ . '/../../data/colleagues.json';
    /** Путь до файла с данными о черном списке
     * @var string
     */
    private string $pathToContactList = __DIR__ . '/../../data/contact_list.json';
    /** Путь до файла с данными о адресах
     * @var string
     */
    private string $pathToAddresses = __DIR__ . '/../../data/address.json';

    /** Путь до файла с пользователями
     * @var string
     */
    private string $pathToUsers = __DIR__ . '/../../data/users.json';
    /** Возвращает ури логина
     * @var string
     */
    private string $loginUri;


    /**
     * @return string
     */
    public function getPathToUsers(): string
    {
        return $this->pathToUsers;
    }

    /**
     * @param string $pathToUsers
     * @return AppConfig
     */
    protected function setPathToUsers(string $pathToUsers): AppConfig
    {
        $this->validateFilePath($pathToUsers);
        $this->pathToUsers = $pathToUsers;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoginUri(): string
    {
        return $this->loginUri;
    }

    /**
     * @param string $loginUri
     * @return AppConfig
     */
    protected function setLoginUri(string $loginUri): AppConfig
    {
        $this->loginUri = $loginUri;
        return $this;
    }


    /**
     * @return string
     */
    public function getPathToAddresses(): string
    {
        return $this->pathToAddresses;
    }

    /**
     * @param string $pathToAddresses
     * @return AppConfig
     */
    protected function setPathToAddresses(string $pathToAddresses): AppConfig
    {
        $this->validateFilePath($pathToAddresses);
        $this->pathToAddresses = $pathToAddresses;
        return $this;
    }


    /**
     * @return string
     */
    public function getPathToContactList(): string
    {
        return $this->pathToContactList;
    }

    /**
     * @param string $pathToContactList
     * @return AppConfig
     */
    protected function setPathToContactList(string $pathToContactList): AppConfig
    {
        $this->validateFilePath($pathToContactList);
        $this->pathToContactList = $pathToContactList;
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
    protected function setPathToLogFile(string $pathToLogFile): AppConfig
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
    protected function setPathToRecipients(string $pathToRecipients): AppConfig
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
    protected function setPathToKinsfolk(string $pathToKinsfolk): AppConfig
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
    protected function setPathToCustomers(string $pathToCustomers): AppConfig
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
    protected function setPathToColleagues(string $pathToColleagues): AppConfig
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
    private function validateFilePath(string $path): void
    {
        if (false === file_exists($path)) {
            throw new ErrorCreateAppConfigException('Некорректный путь до файла с данными');
        }
    }
}
