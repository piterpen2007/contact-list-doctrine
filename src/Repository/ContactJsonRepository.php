<?php

namespace EfTech\ContactList\Repository;

use EfTech\ContactList\Entity\Colleague;
use EfTech\ContactList\Entity\ContactRepositoryInterface;
use EfTech\ContactList\Entity\Customer;
use EfTech\ContactList\Entity\Kinsfolk;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\DataLoader\DataLoaderInterface;

class ContactJsonRepository implements ContactRepositoryInterface
{

    /** Данные о контактах
     * @var array|null
     */
    private ?array $data = null;
    /**
     *
     *
     * @var string
     */
    private string $pathToRecipients;
    /**
     *
     *
     * @var string
     */
    private string $pathToCustomers;
    /**
     *
     *
     * @var string
     */
    private string $pathToKinsfolk;
    /**
     *
     *
     * @var string
     */
    private string $pathToColleagues;

    /**
     *
     *
     * @var DataLoaderInterface
     */
    private DataLoaderInterface $dataLoader;

    /**
     * @param string $pathToRecipients
     * @param string $pathToCustomers
     * @param string $pathToKinsfolk
     * @param string $pathToColleagues
     * @param DataLoaderInterface $dataLoader
     */
    public function __construct(
        string $pathToRecipients,
        string $pathToCustomers,
        string $pathToKinsfolk,
        string $pathToColleagues,
        DataLoaderInterface $dataLoader
    ) {
        $this->pathToRecipients = $pathToRecipients;
        $this->pathToCustomers = $pathToCustomers;
        $this->pathToKinsfolk = $pathToKinsfolk;
        $this->pathToColleagues = $pathToColleagues;
        $this->dataLoader = $dataLoader;
    }

    /** Загружает данные о контактах по категориям
     * @return array
     */
    private function loadData():array
    {
        $customers =$this->dataLoader->loadData($this->pathToCustomers);
        $recipients = $this->dataLoader->loadData($this->pathToRecipients);
        $kinsfolk = $this->dataLoader->loadData($this->pathToKinsfolk);
        $colleague = $this->dataLoader->loadData($this->pathToColleagues);

        return [
            'customers' => $customers,
            'recipients' => $recipients,
            'kinsfolk' => $kinsfolk,
            'colleagues' => $colleague
        ];
    }


    public function findBy(array $searchCriteria): array
    {
        $foundRecipientsOnCategory = [];
        $recipientsOnCategory = $this->loadData();
        if (array_key_exists('category',$searchCriteria)) {
            if ($searchCriteria['category'] === 'customers') {
                foreach ($recipientsOnCategory['customers'] as $customer) {
                    $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
                }
            } elseif ($searchCriteria['category'] === 'recipients') {
                foreach ($recipientsOnCategory['recipients'] as $recipient) {
                    $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
                }
            } elseif ($searchCriteria['category'] === 'kinsfolk') {
                foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                    $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
                }
            } elseif ($searchCriteria['category'] === 'colleagues') {
                foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                    $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
                }
            }
        } else {
            foreach ($recipientsOnCategory['customers'] as $customer) {
                $foundRecipientsOnCategory[] = Customer::createFromArray($customer);
            }
            foreach ($recipientsOnCategory['recipients'] as $recipient) {
                $foundRecipientsOnCategory[] = Recipient::createFromArray($recipient);
            }
            foreach ($recipientsOnCategory['kinsfolk'] as $kinsfolkValue) {
                $foundRecipientsOnCategory[] = Kinsfolk::createFromArray($kinsfolkValue);
            }
            foreach ($recipientsOnCategory['colleagues'] as $colleague) {
                $foundRecipientsOnCategory[] = Colleague::createFromArray($colleague);
            }
        }
        return $foundRecipientsOnCategory;
    }
}