<?php

namespace DoubleOh13\RocketShipIt;

use Illuminate\Support\Facades\Facade;

class RocketShipItFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}
