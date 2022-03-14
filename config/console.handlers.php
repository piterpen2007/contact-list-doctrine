<?php

use EfTech\ContactList\ConsoleCommand\FindColleagues;
use EfTech\ContactList\ConsoleCommand\FindCustomers;
use EfTech\ContactList\ConsoleCommand\FindKinsfolk;
use EfTech\ContactList\ConsoleCommand\FindRecipients;
use EfTech\ContactList\ConsoleCommand\HashStr;

return [
    'find-recipient' => FindRecipients::class,
    'find-customer' => FindCustomers::class,
    'find-kinsfolk' => FindKinsfolk::class,
    'find-colleague' => FindColleagues::class,
    'hash' => HashStr::class
];
