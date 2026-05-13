<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ZoomService;
use App\Models\Kmd\KmdZoomMeetings;
use Firebase\JWT\JWT;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ZoomMeetingController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    // Tampilkan daftar meeting
    // public function index()
    // {

    //     return view('a_kom_digital_pusat.dashboard.zoom.index');
    // }

    public function index()
    {
        // Ambil meeting yang user boleh akses, misal semua meeting yang status scheduled
        // atau meeting yang terkait user_id, pengajar_id, dll sesuai kebutuhan
       $meetings = KmdZoomMeetings::orderBy('start_time', 'asc')->get();

        return view('a_kom_digital_pusat.dashboard.zoom.index', compact('meetings'));
    }

    public function join($zoomMeetingId)
    {
        $meeting = KmdZoomMeetings::where('zoom_meeting_id', $zoomMeetingId)->firstOrFail();

        return view('a_kom_digital_pusat.dashboard.zoom.join', [
            'meetingId' => $meeting->zoom_meeting_id,
            'passcode' => $meeting->passcode ?? '',
            'topic' => $meeting->topic,
        ]);
    }


public function generateSignature(Request $request)
{
    try {
        $request->validate([
            'meeting_number' => 'required|string',
            'role' => 'required|in:0,1',
        ]);

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');

        if (!$sdkKey || !$sdkSecret) {
            Log::error('Zoom SDK key or secret not set in .env');
            return response()->json(['error' => 'SDK key/secret belum diatur'], 500);
        }

        $meetingNumber = $request->input('meeting_number');
        $role = (int) $request->input('role');
        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            'sdkKey' => $sdkKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp,
        ];

        $signature = JWT::encode($payload, $sdkSecret, 'HS256');

        return response()->json(['signature' => $signature]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error in generateSignature: ' . $e->getMessage(), $e->errors());
        return response()->json(['error' => 'Data input tidak valid', 'details' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Error generating Zoom signature: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['error' => 'Gagal generate signature', 'message' => $e->getMessage()], 500);
    }
}




    // Tampilkan form buat meeting baru
    public function create()
    {
        return view('a_kom_digital_pusat.dashboard.zoom.create');
    }

   
public function store(Request $request)
{
    $request->validate([
        'topic' => 'required|string',
        'start_time' => 'required|date',
        'duration' => 'nullable|integer',
        'email' => 'required|email'
    ]);

    try {
        // Panggil service untuk buat meeting di Zoom API
        $zoomMeetingData = $this->zoomService->createMeeting(
            $request->email,
            $request->topic,
            $request->start_time,
            $request->duration ?? 60
        );

        // Parsing dan format start_time ke format MySQL DATETIME
        $startTimeRaw = $zoomMeetingData['start_time'] ?? $request->start_time;
        $startTime = Carbon::parse($startTimeRaw)->format('Y-m-d H:i:s');

        // Simpan ke database
        KmdZoomMeetings::create([
            'id' => Str::uuid()->toString(),
            'zoom_meeting_id' => $zoomMeetingData['id'] ?? $zoomMeetingData['zoom_meeting_id'] ?? null,
            'topic' => $zoomMeetingData['topic'] ?? $request->topic,
            'start_time' => $startTime,
            'duration' => $zoomMeetingData['duration'] ?? ($request->duration ?? 60),
            'host_email' => $zoomMeetingData['host_email'] ?? $request->email,
            'start_url' => $zoomMeetingData['start_url'] ?? null,
            'join_url' => $zoomMeetingData['join_url'] ?? null,
            'status' => $zoomMeetingData['status'] ?? 'scheduled',
            'user_id' => auth()->id(), // simpan user pembuat meeting
            // tambahkan kolom lain jika perlu
        ]);

        return redirect()->route('kom.zoom.index')->with('success', 'Meeting berhasil dibuat dan disimpan.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}



    // Tampilkan form edit meeting
    public function edit($meetingId)
    {
        try {
            $meeting = $this->zoomService->getMeeting($meetingId);
            return view('a_kom_digital_pusat.dashboard.zoom.edit', compact('meeting'));
        } catch (\Exception $e) {
            return redirect()->route('kom.zoom.index')->withErrors(['error' => 'Gagal mengambil data meeting: ' . $e->getMessage()]);
        }
    }

    // Update meeting
    public function update(Request $request, $meetingId)
    {
        $request->validate([
            'topic' => 'sometimes|string',
            'start_time' => 'sometimes|date',
            'duration' => 'sometimes|integer',
            'settings' => 'sometimes|array',
        ]);

        try {
            $this->zoomService->updateMeeting($meetingId, $request->all());

            return redirect()->route('kom.zoom.index')->with('success', 'Meeting berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Hapus meeting
    public function destroy($meetingId)
    {
        try {
            $this->zoomService->deleteMeeting($meetingId);
            return redirect()->route('kom.zoom.index')->with('success', 'Meeting berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('kom.zoom.index')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
