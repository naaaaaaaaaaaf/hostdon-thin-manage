<?php


namespace Hostdon;

use BadMethodCallException;
use RuntimeException;

class CsrfValidator
{
    const HASH_ALGO = 'sha256';
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }
    public static function generate()
    {
        return hash(self::HASH_ALGO, session_id());
    }

    public static function validate($token, $throw = false)
    {
        $success = self::generate() === $token;
        if (!$success && $throw) {
            throw new RuntimeException('CSRF validation failed.', 400);
        }
        return $success;
    }
}