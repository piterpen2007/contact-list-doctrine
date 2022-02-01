<?php

namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Service\SearchContactsService;
use EfTech\ContactList\Service\SearchContactsService\ColleaguesDto;
use EfTech\ContactList\Service\SearchContactsService\CustomerDto;
use EfTech\ContactList\Service\SearchContactsService\KinsfolkDto;
use EfTech\ContactList\Service\SearchContactsService\RecipientDto;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;

class FindContacts implements CommandInterface
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
     * @var SearchContactsService
     */
    private SearchContactsService $searchContactsService;

    /**
     * @param OutputInterface $output
     * @param SearchContactsService $searchContactsService
     */
    public function __construct(OutputInterface $output, SearchContactsService $searchContactsService)
    {
        $this->output = $output;
        $this->searchContactsService = $searchContactsService;
    }


    public static function getShortOption(): string
    {
        return 'n:';
    }

    public static function getLongOption(): array
    {
        return [
            'category:',
        ];
    }

    public function __invoke(array $params): void
    {
        $dtoCollection = $this->searchContactsService->search(
            (new SearchContactsCriteria())
                ->setCategory($params['category'] ?? null)
        );
        $jsonData = $this->buildJsonData($dtoCollection);
        $this->output->print(json_encode(
            $jsonData,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE
        ));
    }

    /**
     * @param object[] $dtoCollection
     *
     * @return array
     */
    private function buildJsonData(array $dtoCollection): array
    {
        $result = [];
        foreach ($dtoCollection as $contactDto) {
            if ($contactDto instanceof CustomerDto) {
                $result[] = [
                    'id_recipient' => $contactDto->getIdRecipient(),
                    'full_name' => $contactDto->getFullName(),
                    'birthday' => $contactDto->getBirthday(),
                    'profession' => $contactDto->getProfession(),
                    'contract_number' => $contactDto->getContactNumber(),
                    'average_transaction_amount' => $contactDto->getAverageTransactionAmount(),
                    'discount' => $contactDto->getDiscount(),
                    'time_to_call' => $contactDto->getTimeToCall()
                ];
            }
            if ($contactDto instanceof ColleaguesDto) {
                $result[] = [
                    'id_recipient' => $contactDto->getIdRecipient(),
                    'full_name' => $contactDto->getFullName(),
                    'birthday' => $contactDto->getBirthday(),
                    'profession' => $contactDto->getProfession(),
                    'department' => $contactDto->getDepartment(),
                    'position' => $contactDto->getPosition(),
                    'room_number' => $contactDto->getRoomNumber()
                ];
            }
            if ($contactDto instanceof KinsfolkDto) {
                $result[] = [
                    'id_recipient' => $contactDto->getIdRecipient(),
                    'full_name' => $contactDto->getFullName(),
                    'birthday' => $contactDto->getBirthday(),
                    'profession' => $contactDto->getProfession(),
                    'status' => $contactDto->getStatus(),
                    'ringtone' => $contactDto->getRingtone(),
                    'hotkey' => $contactDto->getHotkey()
                ];
            }
            if ($contactDto instanceof RecipientDto) {
                $result[] = [
                    'id_recipient' => $contactDto->getIdRecipient(),
                    'full_name' => $contactDto->getFullName(),
                    'birthday' => $contactDto->getBirthday(),
                    'profession' => $contactDto->getProfession(),
                ];
            }
        }
        return $result;
    }
}
