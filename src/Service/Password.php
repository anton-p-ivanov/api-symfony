<?php

namespace App\Service;

/**
 * Class Password
 *
 * Secure password generator.
 *
 * @package App\Service
 */
class Password
{
    /**
     * @param int $length
     *
     * @return mixed|string
     */
    public static function generate(int $length = 6)
    {
        $base = bin2hex(random_bytes($length));
        $specials = str_split('[]\/.+=-_{}()*&^%$:;"#@!');

        for ($i = 0; $i < strlen($base); $i++) {
            if (!is_numeric($base[$i]) && random_int(0, 1)) {
                $base = substr_replace($base, strtoupper($base[$i]), $i, 1);
            }
        }

        for ($i = 0; $i < random_int(3, ceil($length / 2)); $i++) {
            $base = substr_replace($base, $specials[array_rand($specials)], random_int(0, strlen($base)), 1);
        }

        return $base;
    }
}