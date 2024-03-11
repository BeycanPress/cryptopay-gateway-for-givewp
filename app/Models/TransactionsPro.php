<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\GiveWP\Models;

use BeycanPress\CryptoPay\Models\AbstractTransaction;

class TransactionsPro extends AbstractTransaction
{
    public string $addon = 'givewp';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct('givewp_transaction');
    }
}
