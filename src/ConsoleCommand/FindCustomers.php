<?php

namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Service\SearchCustomersService\CustomerDto;
use EfTech\ContactList\Service\SearchCustomersService\SearchCustomersCriteria;
use EfTech\ContactList\Service\SearchCustomersService;
use JsonException;

class FindCustomers implements CommandInterface
{
    /**
     *
     *
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     *
     *
     * @var SearchCustomersService
     */
    private SearchCustomersService $searchCustomersService;

    /**
     *
     * @param OutputInterface $output
     * @param SearchCustomersService $searchCustomersService
     */
    public function __construct(OutputInterface $output, SearchCustomersService $searchCustomersService)
    {
        $this->output = $output;
        $this->searchCustomersService = $searchCustomersService;
    }

    /**
     * @inheritDoc
     */
    public static function getShortOption(): string
    {
        return 'n:';
    }

    /**
     * @inheritDoc
     */
    public static function getLongOption(): array
    {
        return [
            'id_recipient:',
            'full_name:',
            'birthday:',
            'profession:',
            'contract_number:',
            'average_transaction_amount:',
            'discount:',
            'time_to_call'
        ];
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function __invoke(array $params): void
    {
        $dtoCollection = $this->searchCustomersService->search(
            (new SearchCustomersCriteria())
                ->setIdRecipient($params['id_recipient'] ?? null)
                ->setFullName($params['full_name'] ?? null)
                ->setBirthday($params['birthday'] ?? null)
                ->setProfession($params['profession'] ?? null)
                ->setContactNumber($params['contract_number'] ?? null)
                ->setAverageTransactionAmount($params['average_transaction_amount'] ?? null)
                ->setDiscount($params['discount'] ?? null)
                ->setTimeToCall($params['time_to_call'] ?? null)

        );
        $jsonData = $this->buildJsonData($dtoCollection);
        $this->output->print(json_encode($jsonData,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param CustomerDto[] $dtoCollection
     *
     * @return array
     */
    private function buildJsonData(array $dtoCollection):array
    {
        $result = [];
        foreach ($dtoCollection as $customerDto) {
            $result[] = [
                'id_recipient' => $customerDto->getIdRecipient(),
                'full_name' => $customerDto->getFullName(),
                'birthday' => $customerDto->getBirthday(),
                'profession' => $customerDto->getProfession(),
                'contract_number' => $customerDto->getContactNumber(),
                'average_transaction_amount' => $customerDto->getAverageTransactionAmount(),
                'discount' => $customerDto->getDiscount(),
                'time_to_call' => $customerDto->getTimeToCall(),
            ];
        }
        return $result;
    }

}