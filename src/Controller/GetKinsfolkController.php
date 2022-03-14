<?php

namespace EfTech\ContactList\Controller;

/**
 * Получение информации о одном получателе
 */
class GetKinsfolkController extends GetKinsfolkCollectionController
{
    /**
     * @inheritDoc
     */
    protected function buildHttpCode(array $foundKinsfolk): int
    {
        return 0 === count($foundKinsfolk) ? 404 : 200;
    }

    /**
     * @inheritDoc
     */
    protected function buildResult(array $foundKinsfolk): array
    {
        return 1 === count($foundKinsfolk)
            ? $this->serializeRecipient(current($foundKinsfolk))
            : [ 'status' => 'fail', 'message' => 'entity not found'];
    }
}
