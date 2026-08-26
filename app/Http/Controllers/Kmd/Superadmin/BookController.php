<?php
namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\BookRequest;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdCertificates;
use App\Models\Kmd\KomUser;
use App\Services\Superadmin\KomBookService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kmd\KmdBooks;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    protected $service;

    public function __construct(KomBookService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->service->getAll($filters);

        // Debug data
        //dd($data->items(), $filters);

        return view('a_kom_digital_pusat.superadmin.book.index', compact('data', 'filters'));
    }
public function apiNoBooks(Request $request): JsonResponse
{
    // ✅ 3 Table Join: kmd_books → kmd_certificates → kmd_users
    $data = KmdBooks::select(
            'kmd_books.id',
            'kmd_books.no_books',
            'kmd_books.date_publish as books_date',
            'kmd_books.status as books_status',
            'kmd_certificates.no_certificate',
            'kmd_certificates.date_publish as cert_date',
            'kmd_users.name as user_name',
            'kmd_users.no_sim',
        )
        ->leftJoin('kmd_certificates', 'kmd_books.user_id', '=', 'kmd_certificates.user_id')
        ->leftJoin('kmd_users', 'kmd_books.user_id', '=', 'kmd_users.id', 'kmd_users.no_sim')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'no_books' => $item->no_books,           // ✅ No Buku
                'user_name' => $item->user_name ?? '-',   // ✅ Nama User
                'no_sim' => $item->no_sim ?? 'null',          // ✅ No SIM
                'no_certificate' => $item->no_certificate ?? 'null',  // ✅ No Certificate
                'date_publish' => $item->books_date,
                'status' => $item->books_status,
            ];
        });

    return response()->json([
        'data' => $data,
        'total' => $data->count(),
    ]);
}
//     public function apiNoBooks(Request $request): JsonResponse
// {
//     $data = KmdBooks::select('no_books')->get();

//     return response()->json([
//         'data' => $data,
//         'total' => $data->count(),
//     ]);
//    $books = KmdBooks::with(['user' => function($query) {
//         $query->select('id', 'nik', 'name', 'email');
//     }])->select('id', 'user_id', 'no_books')->get();

//     // Format ulang data supaya field user dan buku berada satu level, dan hilangkan id, user_id
//     $data = $books->map(function ($book) {
//         return [
//             'nik' => $book->user->nik ?? null,
//             'name' => $book->user->name ?? null,
//             'email' => $book->user->email ?? null,
//             'no_books' => $book->no_books,
//         ];
//     });

//     return response()->json([
//         'data' => $data,
//         'total' => $data->count(),
//     ]);
// }



    public function create()
    {
        $users = KomUser::all();
        $classes = KmdClass::all();
        $certificates = KmdCertificates::all();

        return view('a_kom_digital_pusat.superadmin.book.create', compact('users', 'classes', 'certificates'));
    }

    public function store(BookRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Tambahkan user_id dari user yang login
            //$data['user_id'] = Auth::id();

            $this->service->store($data);

            DB::commit();
            return redirect()->route('kom.super_books.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan buku: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $book = $this->service->find(Crypt::decrypt($id));
            return view('a_kom_digital_pusat.superadmin.book.edit', compact('book'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit buku: ' . $e->getMessage());
            return redirect()->route('kom.super_books.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(BookRequest $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->validated());
            return redirect()->route('kom.super_books.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate buku: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }

    // public function destroy($id)
    // {
    //     try {
    //         $this->service->delete(Crypt::decrypt($id));
    //         return redirect()->route('kom.super_books.index')->with('success', 'Data berhasil dihapus.');
    //     } catch (\Exception $e) {
    //         Log::error('Gagal menghapus buku: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
    //     }
    // }
    public function destroy($id)
{
    try {
        $this->service->delete($id);

        return redirect()
            ->route('kom.super_books.index')
            ->with('success', 'Data berhasil dihapus.');

    } catch (\Exception $e) {

        Log::error('Gagal menghapus buku: ' . $e->getMessage());

        return redirect()
            ->back()
            ->with('error', 'Terjadi kesalahan saat menghapus data.');
    }
}
}
