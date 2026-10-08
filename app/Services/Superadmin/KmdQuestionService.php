<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdQuestions;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KmdQuestionService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        // kom_v02: hanya pertanyaan dalam bahasa yang sedang dikelola
        $query = KmdQuestions::where('language_code', \App\Services\Kmd\BahasaKelolaService::current());

        if (!empty($filters['search'])) {
            $query->where('questions', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdQuestions
    {
        return KmdQuestions::findOrFail($id);
    }

    public function store(array $data)
{
    // $data['questions'] adalah array pertanyaan
    // $data['status'] adalah status yang sama untuk semua pertanyaan

    $questions = $data['questions'] ?? [];
    $status = $data['status'] ?? 'inactive';

    $created = [];

    foreach ($questions as $question) {
        $created[] = KmdQuestions::create([
            'questions' => $question,
            'status' => $status,
            'language_code' => \App\Services\Kmd\BahasaKelolaService::current(), // kom_v02
        ]);
    }

    return $created; // array model yang baru dibuat
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
