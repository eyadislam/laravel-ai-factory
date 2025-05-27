<?php

namespace FlorianDomgjoni\AIFactory\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FlorianDomgjoni\AIFactory\AIFactory
 */
class AIFactory extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FlorianDomgjoni\AIFactory\AIFactory::class;
    }
}
