<?php

namespace App\Services\Superadmin;

use RuntimeException;

/** Pesan untuk superadmin (aman ditampilkan), dilempar oleh EmailResendService. */
class EmailResendException extends RuntimeException
{
}
