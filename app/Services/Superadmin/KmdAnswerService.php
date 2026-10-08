<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdAnswer;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class KmdAnswerService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        // kom_v02: hanya jawaban dari pertanyaan bahasa yang sedang dikelola
        $query = KmdAnswer::with('question')->whereHas('question', fn ($q) => $q->where('language_code', \App\Services\Kmd\BahasaKelolaService::current()));

        if (!empty($filters['search'])) {
            $query->where('answer', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['questions_id'])) {
            $query->where('questions_id', $filters['questions_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('questions_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
    }


    public function find(string $id): KmdAnswer
    {
        return KmdAnswer::findOrFail($id);
    }

    public function store(array $data)
    {
        // $data berisi array dengan keys: questions_id[], answer[], is_true[], status[]
        // Pastikan semua array memiliki jumlah elemen yang sama

        $questionsIds = $data['questions_id'] ?? [];
        $answers = $data['answer'] ?? [];
        $isTrues = $data['is_true'] ?? [];
        $statuses = $data['status'] ?? [];

        $created = [];

        $count = count($questionsIds);

        for ($i = 0; $i < $count; $i++) {
            $isTrue = isset($isTrues[$i]) && $isTrues[$i] == 1 ? 1 : 0;

            $created[] = KmdAnswer::create([
                'questions_id' => $questionsIds[$i],
                'answer' => $answers[$i],
                'is_true' => $isTrue,
                'status' => $statuses[$i],
            ]);
        }

        return $created; // array model yang baru dibuat
    }


    public function update(string $id, array $data): bool
    {
        $answer = $this->find($id);
        return $answer->update($data);
    }

    public function delete(string $id): bool
    {
        $answer = $this->find($id);
        return $answer->delete();
    }
}
