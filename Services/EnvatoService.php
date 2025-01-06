<?php
namespace Modules\GitEnvato\Services;

class EnvatoService
{
    public function __construct()
    {
        // ...
    }

    public function extractCookies( $command )
    {
        // Match the cookies string within the -H 'cookie: ...' part
        if (preg_match("/-H 'cookie: (.*?)'/", $command, $matches)) {
            $cookieString = $matches[1];

            // Split cookies into key-value pairs
            $cookies = explode("; ", $cookieString);

            // Parse cookies into an associative array
            $cookieArray = [];
            foreach ($cookies as $cookie) {
                list($key, $value) = explode("=", $cookie, 2);
                $cookieArray[trim($key)] = trim($value);
            }

            return $cookieArray;
        }

    }
}