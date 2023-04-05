<?php

namespace MTC\Payments\Facades;

use Illuminate\Support\Facades\Facade;

class MTCPaymentsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'MTC_payments';
    }
}