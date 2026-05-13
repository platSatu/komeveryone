<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdVideo;
use Illuminate\Pagination\AbstractPaginator;

class VideoService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdVideo::with('modul');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdVideo
    {
        return KmdVideo::findOrFail($id);
    }

    public function store(array $data): KmdVideo
    {
        // Cek apakah ada file video di $data['video']
        if (isset($data['video']) && $data['video']->isValid()) {
            // Generate nama file unik
            $filename = time() . '_' . $data['video']->getClientOriginalName();
            // Pindahkan file ke folder public/videos
            $data['video']->move(public_path('videos'), $filename);
            // Simpan path filename ke data yang akan diinsert
            $data['video'] = $filename;
        }

        return KmdVideo::create($data);
    }


    public function update(string $id, array $data): bool
    {
        $video = $this->find($id);
        return $video->update($data);
    }

    public function delete(string $id): bool
    {
        $video = $this->find($id);
        return $video->delete();
    }
}
