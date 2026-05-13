<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\KmdUserQuestionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdQuestions;
use App\Models\Kmd\KmdAnswer;
use App\Models\Kmd\KomUser;

class UserQuestionController extends Controller
{
    protected $service;

    public function __construct(KmdUserQuestionsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.user_questionvideo.index', compact('data', 'filters'));
    }

    public function create()
    {
        $users = KomUser::all();
        $questions = KmdQuestions::all();
        $answers = KmdAnswer::all();

        return view('a_kom_digital_pusat.superadmin.user_questionvideo.create', compact('users', 'questions', 'answers'));
    }

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            return redirect()->route('user_questions.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan user question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = $this->service->find($decryptedId);
            $users = KomUser::all();
            $questions = KmdQuestions::all();
            $answers = KmdAnswer::all();

            return view('a_kom_digital_pusat.superadmin.user_questionvideo.edit', compact('data', 'users', 'questions', 'answers'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit: ' . $e->getMessage());
            return redirect()->route('user_questions.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('user_questions.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate user question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('user_questions.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus user question: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
