<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KmdQuestionRequest; // buat validasi sendiri ya
use App\Services\Superadmin\KmdQuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\Imports\KmdQuestionsImport;
use Maatwebsite\Excel\Concerns\ToModel;


class QuestionController extends Controller
{
    protected $service;

    public function __construct(KmdQuestionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.question.index', compact('data', 'filters'));
    }

    public function create()
    {
        return view('a_kom_digital_pusat.superadmin.question.create');
    }

    public function store(KmdQuestionRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_pertanyaan.index')->with('success', 'Pertanyaan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pertanyaan: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pertanyaan.');
        }
    }

    public function edit($id)
    {
        try {
            $question = $this->service->find($id);
            return view('a_kom_digital_pusat.superadmin.question.edit', compact('question'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit pertanyaan: ' . $e->getMessage());
            return redirect()->route('kom.super_question.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit.');
        }
    }

    public function update(KmdQuestionRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('kom.super_pertanyaan.index')->with('success', 'Pertanyaan berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate pertanyaan: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate pertanyaan.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('kom.super_pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pertanyaan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus pertanyaan.');
        }
    }

    public function importForm()
    {
        return view('a_kom_digital_pusat.superadmin.question.import'); // buat view ini ya
    }

    /**
     * Proses import Excel
     */
    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new KmdQuestionsImport, $request->file('file'));
            return redirect()->route('kom.super_pertanyaan.index')->with('success', 'Data pertanyaan berhasil diimport.');
        } catch (\Exception $e) {
            Log::error('Gagal import pertanyaan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data.');
        }
    }
}
