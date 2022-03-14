<?php

namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Service\SearchKinsfolkService;
use EfTech\ContactList\Service\SearchKinsfolkService\KinsfolkDto;
use EfTech\ContactList\Service\SearchKinsfolkService\SearchKinsfolkCriteria;
use JsonException;

class FindKinsfolk implements CommandInterface
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
     * @var SearchKinsfolkService
     */
    private SearchKinsfolkService $searchKinsfolkService;

    /**
     *
     * @param OutputInterface $output
     * @param SearchKinsfolkService $searchKinsfolkService
     */
    public function __construct(OutputInterface $output, SearchKinsfolkService $searchKinsfolkService)
    {
        $this->output = $output;
        $this->searchKinsfolkService = $searchKinsfolkService;
    }

    /**
     * @inheritDoc
     */
    public static function getShortOption(): string
    {
        return 'i:f:b:p:c:a:d:t';
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
            'status:',
            'ringtone:',
            'hotkey:'
        ];
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function __invoke(array $params): void
    {
        $dtoCollection = $this->searchKinsfolkService->search(
            (new SearchKinsfolkCriteria())
                ->setIdRecipient($params['id_recipient'] ?? null)
                ->setFullName($params['full_name'] ?? null)
                ->setBirthday($params['birthday'] ?? null)
                ->setProfession($params['profession'] ?? null)
                ->setRingtone($params['ringtone'] ?? null)
                ->setHotkey($params['hotkey'] ?? null)
                ->setStatus($params['status'] ?? null)
        );
        $jsonData = $this->buildJsonData($dtoCollection);
        $this->output->print(json_encode(
            $jsonData,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE
        ));
    }

    /**
     * @param KinsfolkDto[] $dtoCollection
     *
     * @return array
     */
    private function buildJsonData(array $dtoCollection): array
    {
        $result = [];
        foreach ($dtoCollection as $kinsfolkDto) {
            $result[] = [
                'id_recipient' => $kinsfolkDto->getIdRecipient(),
                'full_name' => $kinsfolkDto->getFullName(),
                'birthday' => $kinsfolkDto->getBirthday(),
                'profession' => $kinsfolkDto->getProfession(),
                'status' => $kinsfolkDto->getStatus(),
                'ringtone' => $kinsfolkDto->getRingtone(),
                'hotkey' => $kinsfolkDto->getHotkey()
            ];
        }
        return $result;
    }
}
