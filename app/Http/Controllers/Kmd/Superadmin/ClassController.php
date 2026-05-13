<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KomClassRequest;
use App\Services\Superadmin\KmdClassService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassController extends Controller
{
    protected KmdClassService $classService;

    public function __construct(KmdClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(Request $request): View
    {
        try {
            $filters = $request->only(['search', 'status']);
            $perPage = (int) $request->get('per_page', 15);

            $classList = $this->classService->index($filters, $perPage); // Ganti dari $data ke $classList

            return view('a_kom_digital_pusat.superadmin.class.index', compact('classList', 'filters'));
        } catch (\Exception $e) {
            // Create empty paginator for error case
            $classList = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                [
                    'path' => $request->url(),
                    'pageName' => 'page'
                ]
            );

            return view('a_kom_digital_pusat.superadmin.class.index', [
                'classList' => $classList,
                'filters' => $filters ?? [],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }


    public function create(): View
    {
        return view('a_kom_digital_pusat.superadmin.class.create');
    }

    public function store(KomClassRequest $request): RedirectResponse
    {
        try {
            $this->classService->create($request->validated());

            return redirect()
                ->route('kom.super_kelas-kom.index')
                ->with('success', 'Data kelas berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(string $encryptedId): View|RedirectResponse
{
    try {
        $classItem = $this->classService->show($encryptedId);
        return view('a_kom_digital_pusat.superadmin.class.show', compact('classItem'));
    } catch (\Exception $e) {
        return redirect()
            ->route('kom.super_kelas-kom.index')
            ->with('error', 'Data kelas tidak ditemukan.');
    }
}

    public function edit(string $encryptedId): View|RedirectResponse
    {
        try {
            $classItem = $this->classService->show($encryptedId); // Ganti dari $class ke $classItem

            return view('a_kom_digital_pusat.superadmin.class.edit', compact('classItem'));
        } catch (\Exception $e) {
            return redirect()
                ->route('kom.super_kelas-kom.index')
                ->with('error', 'Data kelas tidak ditemukan.');
        }
    }


    public function update(KomClassRequest $request, string $encryptedId): RedirectResponse
    {
        try {
            $this->classService->update($encryptedId, $request->validated());

            return redirect()
                ->route('kom.super_kelas-kom.index')
                ->with('success', 'Data kelas berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(string $encryptedId): RedirectResponse
    {
        try {
            $this->classService->delete($encryptedId);

            return redirect()
                ->route('kom.super_kelas-kom.index')
                ->with('success', 'Data kelas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
