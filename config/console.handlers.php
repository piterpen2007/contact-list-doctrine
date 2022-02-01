<?php

use EfTech\ContactList\ConsoleCommand\FindContacts;
use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\ConsoleCommand\HashStr;

return [
    'find-recipient' => FindRecipients::class,
    'find-customer' => FindCustomers::class,
    'find-contact' => FindContacts::class,
    'hash' => HashStr::class
];
