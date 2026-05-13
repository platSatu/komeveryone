<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdUserQuestions;
use Illuminate\Pagination\AbstractPaginator;

class KmdUserQuestionsService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdUserQuestions::with(['user', 'question', 'answer']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdUserQuestions
    {
        return KmdUserQuestions::with(['user', 'question', 'answer'])->findOrFail($id);
    }

    public function store(array $data): KmdUserQuestions
    {
        return KmdUserQuestions::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $question = $this->find($id);
        return $question->update($data);
    }

    public function delete(string $id): bool
    {
        $question = $this->find($id);
        return $question->delete();
    }
}
