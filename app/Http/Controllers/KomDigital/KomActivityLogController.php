<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomActivityLog;
use App\Models\Kom\KomUser;

class KomActivityLogController extends Controller
{
    public function index()
    {
        $logs = KomActivityLog::where('user_id', auth()->id())->latest()->get();
        return view('kom_digital.logs.index', compact('logs'));
    }

    public function create()
    {
        $users = KomUser::all();
        return view('kom_digital.activity_log.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:kom_users,id',
            'aksi' => 'required|string',
        ]);

        KomActivityLog::create([
            'user_id' => $request->user_id,
            'aksi' => $request->aksi,
            'timestamp' => now(),
        ]);

        return redirect()->route('kom.activity_log.index')->with('success', 'Log aktivitas dicatat');
    }


}
