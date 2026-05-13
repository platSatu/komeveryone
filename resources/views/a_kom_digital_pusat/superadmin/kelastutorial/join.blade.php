@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4>Topik: {{ $topic }}</h4>

                {{-- Container Zoom Meeting --}}
                <div id="meetingSDKElement" style="width: 100%; height: 600px;"></div>

                {{-- Pesan error jika gagal --}}
                <div id="zoomErrorMessage" style="display: none; color: red; margin-top: 20px;">
                    Gagal memuat Zoom Meeting. Silakan coba lagi nanti atau hubungi admin.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Load dependensi Zoom Web SDK --}}
<script src="https://source.zoom.us/2.11.4/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/2.11.4/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/2.11.4/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/2.11.4/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/2.11.4/lib/vendor/lodash.min.js"></script>

{{-- Load Zoom Meeting Embedded SDK --}}
<script src="https://source.zoom.us/2.11.4/zoom-meeting-embedded.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    if (!window.ZoomMtgEmbedded) {
        console.error('Zoom SDK tidak tersedia.');
        document.getElementById('zoomErrorMessage').style.display = 'block';
        return;
    }

    const meetingSDKElement = document.getElementById('meetingSDKElement');
    const client = window.ZoomMtgEmbedded.createClient();

    client.init({
        zoomAppRoot: meetingSDKElement,
        language: 'en-US',
        patchJsMedia: true,
        customize: {
            video: {
                isResizable: true,
                popper: {
                    disableDraggable: false
                }
            }
        }
    });

    const meetingNumber = '{{ $meetingId }}';
    const passcode = '{{ $passcode }}';
    const userName = '{{ auth()->user()->name ?? "Peserta" }}';
    const userEmail = '{{ auth()->user()->email ?? "" }}';
    const sdkKey = '{{ env("ZOOM_SDK_KEY") }}';
    const role = 0; // 0 = peserta, 1 = host

    try {
        const response = await fetch('{{ route("kom.zoom.zoom.meetings.signature") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                meeting_number: meetingNumber,
                role: role
            })
        });

        const data = await response.json();

        if (data.signature) {
            await client.join({
                signature: data.signature,
                sdkKey: sdkKey,
                meetingNumber: meetingNumber,
                password: passcode,
                userName: userName,
                userEmail: userEmail,
            });
        } else {
            console.error('Signature tidak ditemukan:', data);
            document.getElementById('zoomErrorMessage').style.display = 'block';
        }
    } catch (error) {
        console.error('Gagal join Zoom:', error);
        document.getElementById('zoomErrorMessage').style.display = 'block';
    }
});
</script>

@endsection