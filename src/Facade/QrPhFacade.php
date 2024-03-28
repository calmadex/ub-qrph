<?php

namespace Ezi\UbQrPh\Facade;

use Illuminate\Support\Facades\Facade;  

class QrPhFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ub-qrph';
    }
}