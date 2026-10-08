<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdVideo;
use Illuminate\Pagination\AbstractPaginator;

class VideoService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        // kom_v02: hanya video dari modul bahasa yang sedang dikelola
        $query = KmdVideo::with('modul')->whereHas('modul', fn ($q) => $q->where('language_code', \App\Services\Kmd\BahasaKelolaService::current()));

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

        // kom_v02: unggah file video baru saat edit (mis. versi bahasa lain).
        // Sebelumnya objek file ikut tersimpan apa adanya ke kolom `video`.
        if (isset($data['video']) && $data['video'] instanceof \Illuminate\Http\UploadedFile && $data['video']->isValid()) {
            $filename = time() . '_' . $data['video']->getClientOriginalName();
            $data['video']->move(public_path('videos'), $filename);
            $data['video'] = $filename;
        } else {
            unset($data['video']); // tidak ada file baru → pertahankan file lama
        }

        return $video->update($data);
    }

    public function delete(string $id): bool
    {
        $video = $this->find($id);
        return $video->delete();
    }
}
