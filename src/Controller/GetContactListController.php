<?php

namespace EfTech\ContactList\Controller;

class GetContactListController extends GetContactListCollectionController
{
    /**
     * @inheritDoc
     */
    protected function buildHttpCode(array $foundContactList): int
    {
        return 0 === count($foundContactList) ? 404 : 200;
    }

    /**
     * @inheritDoc
     */
    protected function buildResult(array $foundContactLists)
    {
        return 1 === count($foundContactLists)
            ? $this->serializeContactList(current($foundContactLists))
            : [ 'status' => 'fail', 'message' => 'entity not found'];
    }
}
