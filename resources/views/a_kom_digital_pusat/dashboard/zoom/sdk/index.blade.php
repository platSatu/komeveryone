@extends('layouts.KomDigital')

@section('content')
<h1>Zoom Web SDK Demo</h1>

    <div>
        <label>Meeting Number:</label>
        <input type="text" id="meetingNumber" value="YOUR_MEETING_NUMBER" />
    </div>
    <div>
        <label>Your Name:</label>
        <input type="text" id="userName" value="Laravel User" />
    </div>
    <div>
        <label>Role:</label>
        <select id="role">
            <option value="0">Participant</option>
            <option value="1">Host</option>
        </select>
    </div>
    <button id="joinBtn">Join Meeting</button>

    <div id="zmmtg-root"></div>

    <!-- Zoom Web SDK -->
    <script src="https://source.zoom.us/2.13.0/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/2.13.0/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/2.13.0/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/2.13.0/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/2.13.0/lib/vendor/jquery.min.js"></script>
    <script src="https://source.zoom.us/2.13.0/zoom-meeting-2.13.0.min.js"></script>

    <script>
        ZoomMtg.setZoomJSLib('https://source.zoom.us/2.13.0/lib', '/av');

        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareJssdk();

        document.getElementById('joinBtn').addEventListener('click', function () {
            const meetingNumber = document.getElementById('meetingNumber').value;
            const userName = document.getElementById('userName').value;
            const role = parseInt(document.getElementById('role').value);

            if (!meetingNumber) {
                alert('Meeting Number harus diisi');
                return;
            }

            fetch("{{ route('kom.zoom.zoom.sdk.signature') }}", {
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
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                const signature = data.signature;

                ZoomMtg.init({
                    leaveUrl: window.location.href,
                    isSupportAV: true,
                    success: function () {
                        ZoomMtg.join({
                            signature: signature,
                            meetingNumber: meetingNumber,
                            userName: userName,
                            sdkKey: '{{ env("ZOOM_SDK_KEY") }}',
                            userEmail: '', // optional
                            passWord: '', // optional
                            success: function () {
                                console.log('Join meeting success');
                            },
                            error: function (err) {
                                console.error(err);
                                alert('Gagal join meeting: ' + JSON.stringify(err));
                            }
                        });
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Gagal inisialisasi Zoom SDK: ' + JSON.stringify(err));
                    }
                });
            })
            .catch(err => {
                console.error(err);
                alert('Gagal generate signature: ' + err.message);
            });
        });
    </script>
@endsection
