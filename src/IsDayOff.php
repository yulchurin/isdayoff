<?php

namespace Mactape\IsDayOff;

/**
 * @method static bool check(?\DateTimeInterface $date = null)
 * @method static string fileContent(?\DateTimeInterface $date)
 */
class IsDayOff extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mactape\IsDayOff\ApiCheck::class;
    }
}
