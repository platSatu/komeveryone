<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\QuestionVideoRequest;
use App\Services\Superadmin\KmdQuestionVideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdQuestions;
use App\Models\Kmd\KmdVideo;
use App\Rules\CustomDbRule;

class QuestionVideosController extends Controller
{
    protected $service;

    public function __construct(KmdQuestionVideoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
{
    $filters = $request->only(['questions_id', 'videos_id', 'status', 'search']); // tambah search
    $data = $this->service->getAll($filters);

    return view('a_kom_digital_pusat.superadmin.question_video.index', compact('data', 'filters'));
}


    public function create()
    {
        // Jika perlu, ambil data video dan pertanyaan untuk form
        $videos = KmdVideo::orderBy('created_at', 'asc')->get();
        $questions = KmdQuestions::orderBy('created_at', 'asc')->get();

        return view('a_kom_digital_pusat.superadmin.question_video.create', compact('videos', 'questions'));
    }

    public function store(QuestionVideoRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_pertanyaan-video.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data question video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->service->find($id);

            // Ambil semua data video dan pertanyaan untuk form dropdown
            $videos = KmdVideo::all();
            $questions = KmdQuestions::all();

            return view('a_kom_digital_pusat.superadmin.question_video.edit', compact('data', 'videos', 'questions'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit question video: ' . $e->getMessage());
            return redirect()->route('kom.super_pertanyaan-video.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit.');
        }
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'videos_id' => ['required', new CustomDbRule('mysql2', 'kmd_videos', 'id', 'exists')],
            'questions_id' => ['required', new CustomDbRule('mysql2', 'kmd_questions', 'id', 'exists')],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'videos_id.required' => 'Video wajib dipilih.',
            'questions_id.required' => 'Pertanyaan wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus berupa active atau inactive.',
        ]);

        try {
            $this->service->update($id, $validated);
            return redirect()->route('kom.super_pertanyaan-video.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            \Log::error('Gagal mengupdate data question video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }


    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('kom.super_pertanyaan-video.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data question video: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
