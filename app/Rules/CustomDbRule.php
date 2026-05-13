<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CustomDbRule implements Rule
{
    protected string $connection;
    protected string $table;
    protected string $column;
    protected string $type; // 'exists' atau 'unique'
    protected $ignoreId;

    /**
     * @param string $connection Nama koneksi database, misal 'mysql2'
     * @param string $table Nama tabel
     * @param string $column Nama kolom untuk validasi (default 'id')
     * @param string $type Tipe validasi: 'exists' atau 'unique'
     * @param mixed|null $ignoreId ID yang diabaikan (hanya untuk unique)
     */
    public function __construct(
        string $connection,
        string $table,
        string $column = 'id',
        string $type = 'exists',
        $ignoreId = null
    ) {
        $this->connection = $connection;
        $this->table = $table;
        $this->column = $column;
        $this->type = $type;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        if (!in_array($this->type, ['exists', 'unique'])) {
            throw new \InvalidArgumentException("Invalid validation type: {$this->type}");
        }

        $query = DB::connection($this->connection)
            ->table($this->table)
            ->where($this->column, $value);

        if ($this->type === 'unique') {
            if ($this->ignoreId !== null) {
                $query->where('id', '!=', $this->ignoreId);
            }
            return !$query->exists();
        }

        return $query->exists();
    }


    public function message(): string
    {
        if ($this->type === 'unique') {
            return "The {$this->column} has already";
        }

        return "The selected {$this->column}";
    }
}
