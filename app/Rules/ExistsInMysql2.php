<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsInMysql2 implements Rule
{
    protected $table;
    protected $column;

    public function __construct(string $table, string $column = 'id')
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function passes($attribute, $value): bool
    {
        return DB::connection('mysql2')
            ->table($this->table)
            ->where($this->column, $value)
            ->exists();
    }

    public function message(): string
    {
        return "The selected {$this->column} is invalid.";
    }
}
