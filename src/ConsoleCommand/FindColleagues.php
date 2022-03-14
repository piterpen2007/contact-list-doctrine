<?php

namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Service\SearchColleagueService;
use EfTech\ContactList\Service\SearchColleagueService\SearchColleagueCriteria;

class FindColleagues implements CommandInterface
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
     * @var SearchColleagueService
     */
    private SearchColleagueService $searchColleagueService;

    /**
     *
     * @param OutputInterface $output
     * @param SearchColleagueService $searchColleagueService
     */
    public function __construct(OutputInterface $output, SearchColleagueService $searchColleagueService)
    {
        $this->output = $output;
        $this->searchColleagueService = $searchColleagueService;
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
            'department:',
            'position:',
            'room_number:'
        ];
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $params): void
    {
        $dtoCollection = $this->searchColleagueService->search(
            (new SearchColleagueCriteria())
                ->setIdRecipient($params['id_recipient'] ?? null)
                ->setFullName($params['full_name'] ?? null)
                ->setBirthday($params['birthday'] ?? null)
                ->setProfession($params['profession'] ?? null)
                ->setPosition($params['position'] ?? null)
                ->setRoomNumber($params['room_number'] ?? null)
                ->setDepartment($params['department'] ?? null)
        );
        $jsonData = $this->buildJsonData($dtoCollection);
        $this->output->print(json_encode(
            $jsonData,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE
        ));
    }

    /**
     * @param SearchColleagueService\ColleagueDto[] $dtoCollection
     *
     * @return array
     */
    private function buildJsonData(array $dtoCollection): array
    {
        $result = [];
        foreach ($dtoCollection as $colleagueDto) {
            $result[] = [
                'id_recipient' => $colleagueDto->getIdRecipient(),
                'full_name' => $colleagueDto->getFullName(),
                'birthday' => $colleagueDto->getBirthday(),
                'profession' => $colleagueDto->getProfession(),
                'department' => $colleagueDto->getDepartment(),
                'position' => $colleagueDto->getPosition(),
                'room_number' => $colleagueDto->getRoomNumber()
            ];
        }
        return $result;
    }
}
