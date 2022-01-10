<?php

namespace EfTech\ContactList\Controller;

/**
 * Получение информации о одном получателе
 */
class GetRecipientsController extends GetRecipientsCollectionController
{
    /**
     * @inheritDoc
     */
    protected function buildHttpCode(array $foundRecipients): int
    {
        return 0 === count($foundRecipients) ? 404 : 200;
    }

    /**
     * @inheritDoc
     */
    protected function buildResult(array $foundRecipients):array
    {
        return 1 === count($foundRecipients)
            ? $this->serializeRecipient(current($foundRecipients))
            : [ 'status' => 'fail', 'message' => 'entity not found'];

    }

}