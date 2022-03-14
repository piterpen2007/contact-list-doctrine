<?php

namespace EfTech\ContactList\Controller;

/**
 * Получение информации о одном получателе
 */
class GetColleaguesController extends GetColleaguesCollectionController
{
    /**
     * @inheritDoc
     */
    protected function buildHttpCode(array $foundColleagues): int
    {
        return 0 === count($foundColleagues) ? 404 : 200;
    }

    /**
     * @inheritDoc
     */
    protected function buildResult(array $foundColleagues): array
    {
        return 1 === count($foundColleagues)
            ? $this->serializeRecipient(current($foundColleagues))
            : [ 'status' => 'fail', 'message' => 'entity not found'];
    }
}
