<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdQuestionVideos;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KmdQuestionVideoService
{
    /**
     * Mendapatkan semua data dengan filter.
     *
     * @param array $filters
     * @return AbstractPaginator
     */
    public function getAll(array $filters = []): AbstractPaginator
{
    $query = KmdQuestionVideos::with(['video', 'question']);

    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->whereHas('video', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })
        ->orWhereHas('question', function($q) use ($search) {
            $q->where('questions', 'like', "%{$search}%");
        });
    }

    // filter lain seperti sebelumnya
    if (!empty($filters['questions_id'])) {
        $query->where('questions_id', $filters['questions_id']);
    }
    if (!empty($filters['videos_id'])) {
        $query->where('videos_id', $filters['videos_id']);
    }
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    return $query->latest()->paginate(10)->withQueryString();
}


    /**
     * Mencari data berdasarkan ID.
     *
     * @param string $id
     * @return KmdQuestionVideos
     */
    public function find(string $id): KmdQuestionVideos
    {
        return KmdQuestionVideos::findOrFail($id);
    }

    /**
     * Menyimpan data baru.
     *
     * @param array $data
     * @return KmdQuestionVideos
     */
    /**
 * Simpan multiple relasi video-pertanyaan.
 *
 * @param array $data Array berisi keys: videos_id[], questions_id[], status[]
 * @return KmdQuestionVideos[] Array model yang baru dibuat
 */
public function store(array $data): array
{
    $videosIds = $data['videos_id'] ?? [];
    $questionsIds = $data['questions_id'] ?? [];
    $statuses = $data['status'] ?? [];

    $count = count($videosIds);
    $created = [];

    for ($i = 0; $i < $count; $i++) {
        $created[] = KmdQuestionVideos::create([
            'videos_id' => $videosIds[$i],
            'questions_id' => $questionsIds[$i],
            'status' => $statuses[$i],
        ]);
    }

    return $created;
}


    /**
     * Memperbarui data.
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $record = $this->find($id);
        return $record->update($data);
    }

    /**
     * Menghapus data berdasarkan ID.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $record = $this->find($id);
        return $record->delete();
    }
}
