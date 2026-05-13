<?php

namespace App\Models\Kmd;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // Gunakan koneksi mysql2 agar token disimpan dan dibaca dari DB yang benar
    protected $connection = 'mysql';
}
