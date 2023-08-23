<?php

namespace Mactape\IsDayOff;

/**
 * @method static bool check(?\Carbon\CarbonInterface $date = null)
 */
class IsDayOff extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mactape\IsDayOff\ApiCheck::class;
    }
}
