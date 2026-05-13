@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Topic: {{ $topic }}</h4>

                    <div id="meetingSDKElement" style="width: 100%; height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Load Zoom Web SDK Embedded --}}
    <script src="https://source.zoom.us/2.13.0/zoom-meeting-embedded.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const client = window.ZoomMtgEmbedded.createClient();
            const meetingSDKElement = document.getElementById('meetingSDKElement');

            client.init({
                zoomAppRoot: meetingSDKElement,
                language: 'en-US',
                patchJsMedia: true
            });

            const meetingNumber = '{{ $meetingId }}';
            const role = 0; // peserta biasa
            const userName = '{{ auth()->user()->name ?? 'Peserta' }}';
            const userEmail = '{{ auth()->user()->email ?? '' }}';
            const passcode = '{{ $passcode }}';

            fetch('{{ route('kom.zoom.zoom.meetings.signature') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        meeting_number: meetingNumber,
                        role: role
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.signature) {
                        client.join({
                            signature: data.signature,
                            sdkKey: '{{ env('ZOOM_SDK_KEY') }}',
                            meetingNumber: meetingNumber,
                            password: passcode,
                            userName: userName,
                            userEmail: userEmail,
                        });
                    } else {
                        alert("Gagal ambil signature: " + (data.error ?? 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error("Zoom join error:", err);
                    alert("Gagal join meeting Zoom");
                });
        });
    </script>
@endsection