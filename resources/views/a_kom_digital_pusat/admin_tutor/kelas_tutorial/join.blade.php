@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4>Topik: {{ $topic }}</h4>

                <!-- Tampilkan informasi meeting -->
                <div style="margin-bottom: 20px;">
                    <p><strong>Meeting ID:</strong> {{ $meetingId }}</p>
                    <p><strong>Passcode:</strong> {{ $passcode }}</p>
                    @if(!empty($joinUrl))
                        <p><strong>Join URL:</strong> <a href="{{ $joinUrl }}" target="_blank">{{ $joinUrl }}</a></p>
                    @endif
                </div>

                <!-- Ganti container Zoom dengan iFrame -->
                <iframe
                    src="{{ route('zoom.meeting.iframe', ['meetingId' => $meetingId]) }}"
                    style="width: 100%; height: 600px; border: none;"
                    allow="camera; microphone; display-capture"
                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
                    title="Zoom Meeting">
                </iframe>

                <div id="zoomErrorMessage" style="display:none; color:red; margin-top:20px;">
                    Gagal memuat Zoom Meeting. Silakan coba lagi nanti atau hubungi admin.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
