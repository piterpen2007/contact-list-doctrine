<?php
namespace EfTech\ContactList\ConsoleCommand;

use EfTech\ContactList\Infrastructure\Console\Output\OutputInterface;
use EfTech\ContactList\Infrastructure\Console\CommandInterface;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsCriteria;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsService;
use JsonException;

final class FindRecipients implements CommandInterface
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
     * @var SearchRecipientsService
     */
    private SearchRecipientsService $searchRecipientsService;

    /**
     *
     * @param OutputInterface $output
     * @param SearchRecipientsService $searchRecipientsService
     */
    public function __construct(OutputInterface $output, SearchRecipientsService $searchRecipientsService)
    {
        $this->output = $output;
        $this->searchRecipientsService = $searchRecipientsService;
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
        ];
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function __invoke(array $params): void
    {
        $dtoCollection = $this->searchRecipientsService->search(
            (new SearchRecipientsCriteria())
                ->setIdRecipient($params['id_recipient'] ?? null)
                ->setFullName($params['full_name'] ?? null)
                ->setBirthday($params['birthday'] ?? null)
                ->setProfession($params['profession'] ?? null)

        );
        $jsonData = $this->buildJsonData($dtoCollection);
        $this->output->print(json_encode($jsonData,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE));
    }
    /**
     * @param RecipientDto[] $dtoCollection
     *
     * @return array
     */
    private function buildJsonData(array $dtoCollection):array
    {
        $result = [];
        foreach ($dtoCollection as $recipientDto) {
            $result[] = [
                'id_recipient' => $recipientDto->getIdRecipient(),
                'full_name' => $recipientDto->getFullName(),
                'birthday' => $recipientDto->getBirthday(),
                'profession' => $recipientDto->getProfession(),
            ];
        }
        return $result;
    }
}