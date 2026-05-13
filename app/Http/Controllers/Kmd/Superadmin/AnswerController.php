<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KmdAnswerRequest;
use App\Services\Superadmin\KmdAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdQuestions;

class AnswerController extends Controller
{
    protected $service;

    public function __construct(KmdAnswerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'questions_id', 'status']);
        $data = $this->service->getAll($filters); // langsung paginate
        $questions = KmdQuestions::orderBy('questions')->get();
        return view('a_kom_digital_pusat.superadmin.answer.index', compact('data', 'filters', 'questions'));
    }


    public function create()
    {
        // Ambil semua pertanyaan, urutkan berdasarkan created_at ascending (terlama di atas)
        $pertanyaans = KmdQuestions::orderBy('created_at', 'asc')->get();

        return view('a_kom_digital_pusat.superadmin.answer.create', compact('pertanyaans'));
    }


    public function store(KmdAnswerRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_jawaban.index')->with('success', 'Jawaban berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan jawaban: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan jawaban.');
        }
    }



    public function edit($id)
    {
        try {
            $answer = $this->service->find($id);
            $pertanyaans = KmdQuestions::orderBy('questions')->get(); // ambil daftar pertanyaan
            return view('a_kom_digital_pusat.superadmin.answer.edit', compact('answer', 'pertanyaans'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit jawaban: ' . $e->getMessage());
            return redirect()->route('kom.super_answer.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit.');
        }
    }



    public function update(KmdAnswerRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('kom.super_jawaban.index')->with('success', 'Jawaban berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate jawaban: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate jawaban.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('kom.super_jawaban.index')->with('success', 'Jawaban berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus jawaban: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus jawaban.');
        }
    }
}
