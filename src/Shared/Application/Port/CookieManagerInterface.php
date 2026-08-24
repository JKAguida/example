<?php

namespace App\Shared\Application\Port;

/** @phpstan-type CookieOptions array{expires:int,httpOnly:bool,secure:bool,sameSite:string} */
interface CookieManagerInterface {
    /** @param CookieOptions $options*/
    public function set(string $key,string $value, array $options):void;
    public function delete(string $key):void;
    public function get(string $key):?string;
}