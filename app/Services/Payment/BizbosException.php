<?php

namespace App\Services\Payment;

use RuntimeException;

/** kom_v02 — error dari API Bizbos (membawa status HTTP & detail validasi). */
class BizbosException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 0, public readonly array $errors = [])
    {
        parent::__construct($message, $status);
    }
}
