@extends('layouts.kom')
@section('content')
    @php
        use App\Models\Kom\KomUserJawabanPertanyaan;

        $answeredCorrect = KomUserJawabanPertanyaan::where('kom_user_id', auth('kom')->id())
            ->where('jawaban_benar', 1)
            ->pluck('pertanyaan_id')
            ->toArray();
    @endphp

    @php
        $userId = auth('kom')->id();
        $videoProgress = \App\Models\Kom\KomUserVideoProgress::where('kom_user_id', $userId)
            ->pluck('is_locked', 'kom_video_id')
            ->toArray();
    @endphp
    <style>
        .disabled-video {
            pointer-events: none;
            opacity: 0.5;
            text-decoration: none;
        }
    </style>

    <div class="row">
        <div class="col-12 col-md-9">
            <div class="card">
                <div class="card-header">Welcome</div>
                <div class="card-body">
                    @if ($komModuls->isNotEmpty())
                        @php
                            $modul1Videos = $komModuls->first()->videos;
                        @endphp

                        <h4>{{ $komModuls->first()->title }}</h4>
                        <p class="text-muted">{{ $komModuls->first()->description }}</p>
                        <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
                        <video id="videoPlayer" playsinline width="100%" controls>
                            <source src="{{ asset('videos/opening.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Speed control -->
                        <div id="popup"
                            style="display:none; position: fixed; top: 30%; left: 50%; transform: translate(-50%, -50%);
background: white; padding: 20px; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 9999;">
                            <!-- Isi popup nanti kamu bisa ganti sendiri -->
                            <p>Welcome</p>
                            <button onclick="document.getElementById('popup').style.display='none'">Tutup</button>
                        </div>
                        <!-- Container pertanyaan -->
                        <div id="question-section" class="mt-4" style="display: none;">
                            <h5>Pertanyaan:</h5>
                            <form id="answerForm">
                                <div id="question-container"></div>
                                <button type="submit" class="btn btn-primary mt-3">Kirim Jawaban</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mt-4 mt-md-0">
            <div class="card">
                <div class="card-body">
                    <h4>Video Lecture</h4>
                    @foreach ($komModuls as $modulIndex => $modul)
                        <div class="card border mb-3">
                            <div class="card-header">{{ $modul->title }}</div>
                            <div class="card-body">
                                @foreach ($modul->videos as $index => $video)
                                    @php
                                        $isUnlocked = false;

                                        if ($modulIndex === 0 && $index === 0) {
                                            // Video pertama dari modul pertama
                                            $isUnlocked = true;
                                        } elseif ($index === 0 && $modulIndex > 0) {
                                            // Video pertama dari modul selanjutnya
                                            $previousModul = $komModuls[$modulIndex - 1];
                                            $lastVideoPrevModul = $previousModul->videos->last();
                                            $isUnlocked = in_array($lastVideoPrevModul->id, $userVideoProgress);
                                        } else {
                                            // Video lainnya dalam modul
                                            $prevVideoId = $modul->videos[$index - 1]->id;
                                            $isUnlocked = in_array($prevVideoId, $userVideoProgress);
                                        }
                                    @endphp


                                    {{-- <p>
                                        <a href="#" class="video-link {{ $isUnlocked ? '' : 'disabled-video' }}"
                                            data-index="{{ $index }}" data-id="{{ $video->id }}"
                                            data-video="{{ asset('videos/' . $video->videos) }}">
                                            {{ $video->name }}
                                            @if ($isUnlocked)
                                                <i class="mdi mdi-lock-open-outline text-success ml-2"></i>
                                            @else
                                                <i class="mdi mdi-lock-outline text-danger ml-2"></i>
                                            @endif
                                        </a>
                                    </p> --}}
                                    @php

                                        // Cek apakah video ini sudah dibuka
                                        $isUnlocked = false;

                                        if ($modulIndex === 0 && $index === 0) {
                                            // Video pertama selalu terbuka
                                            $isUnlocked = true;
                                        } elseif (
                                            isset($videoProgress[$video->id]) &&
                                            $videoProgress[$video->id] == 0
                                        ) {
                                            // Jika progress tersimpan dan is_locked = 0 maka terbuka
                                            $isUnlocked = true;
                                        }
                                    @endphp

                                    <p>
                                        <a href="#" class="video-link {{ $isUnlocked ? '' : 'disabled-video' }}"
                                            data-index="{{ $index }}" data-id="{{ $video->id }}"
                                            data-video="{{ asset('videos/' . $video->videos) }}">
                                            {{ $video->name }}
                                            @if ($isUnlocked)
                                                <i class="mdi mdi-lock-open-outline text-success ml-2"></i>
                                            @else
                                                <i class="mdi mdi-lock-outline text-danger ml-2"></i>
                                            @endif
                                        </a>
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const videoSource = videoPlayer.querySelector('source');
        const questionSection = document.getElementById('question-section');
        const questionContainer = document.getElementById('question-container');
        const answerForm = document.getElementById('answerForm');
        let currentVideoId = null;

        const komModuls = @json($komModuls); // Data modul
        let userVideoProgress = @json($userVideoProgress); // Video yang sudah ditonton
        let userAnsweredCorrect = @json($videoProgress); // Video yang sudah dijawab BENAR

        // Klik video
        document.querySelectorAll('.video-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (this.classList.contains('disabled-video')) return;

                const videoUrl = this.dataset.video;
                currentVideoId = parseInt(this.dataset.id);

                videoSource.src = videoUrl;
                videoPlayer.load();
                videoPlayer.play();

                questionSection.style.display = 'none';
                questionContainer.innerHTML = '';
            });
        });

        // Saat video selesai
        videoPlayer.addEventListener('ended', function() {
            if (!currentVideoId) return;

            // Kalau user SUDAH menjawab video ini dengan benar sebelumnya
            if (userAnsweredCorrect[currentVideoId] === 1) {
                unlockNextVideo(); // Langsung buka video selanjutnya
            } else {
                const lanjut = confirm("Video selesai. Lanjut ke pertanyaan?");
                if (lanjut) {
                    loadQuestions(currentVideoId);
                }

                // Tetap simpan progress menonton video
                fetch(`https://gbigatsu.id/kom-digital/kom/simpan-progress`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            video_id: currentVideoId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log("Progress saved:", data);
                        if (!userVideoProgress.includes(currentVideoId)) {
                            userVideoProgress.push(currentVideoId);
                        }
                    })
                    .catch(err => console.error("Save progress error:", err));
            }
        });

        // Fungsi load pertanyaan
        function loadQuestions(videoId) {
            fetch(`https://gbigatsu.id/kom-digital/kom/pertanyaan/${videoId}`)
                .then(res => res.json())
                .then(data => {
                    questionContainer.innerHTML = '';
                    if (data.length === 0) {
                        questionContainer.innerHTML = '<p class="text-muted">Tidak ada pertanyaan untuk video ini.</p>';
                    } else {
                        data.forEach((item, index) => {
                            const qWrapper = document.createElement('div');
                            qWrapper.classList.add('mb-4');

                            const qTitle = document.createElement('p');
                            qTitle.innerHTML = `<strong>${index + 1}. ${item.pertanyaan}</strong>`;
                            qWrapper.appendChild(qTitle);

                            if (item.jawaban && item.jawaban.length > 0) {
                                item.jawaban.forEach(j => {
                                    const formCheck = document.createElement('div');
                                    formCheck.classList.add('form-check');

                                    const input = document.createElement('input');
                                    input.classList.add('form-check-input');
                                    input.type = 'radio';
                                    input.name = `jawaban[${item.id}]`;
                                    input.value = j.id;
                                    input.id = `jawaban-${j.id}`;

                                    const label = document.createElement('label');
                                    label.classList.add('form-check-label');
                                    label.htmlFor = `jawaban-${j.id}`;
                                    label.textContent = j.jawaban;

                                    formCheck.appendChild(input);
                                    formCheck.appendChild(label);
                                    qWrapper.appendChild(formCheck);
                                });
                            } else {
                                qWrapper.innerHTML +=
                                    '<p class="text-muted">Tidak ada jawaban untuk pertanyaan ini.</p>';
                            }

                            questionContainer.appendChild(qWrapper);
                        });
                    }
                    questionSection.style.display = 'block';
                })
                .catch(err => {
                    console.error('Gagal load pertanyaan:', err);
                    questionContainer.innerHTML = '<p class="text-danger">Error saat memuat pertanyaan.</p>';
                    questionSection.style.display = 'block';
                });
        }

        // Submit jawaban
        answerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(answerForm);

            fetch('https://gbigatsu.id/kom-digital/kom/jawaban', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'benar') {
                        alert('Jawaban benar! Video berikutnya telah dibuka.');
                        userAnsweredCorrect[currentVideoId] = 1; // Tandai bahwa sudah dijawab dengan benar
                        unlockNextVideo();
                    } else {
                        alert('Jawaban salah atau belum lengkap.');
                    }
                })
                .catch(err => {
                    console.error('Submit error:', err);
                    alert('Terjadi kesalahan. Coba lagi nanti.');
                });
        });

        function unlockNextVideo() {
            const currentModulIndex = getCurrentModulIndexByVideoId(currentVideoId);

            if (isModulCompleted(currentModulIndex)) {
                showModuleCompleteOptions();
                return;
            }

            const nextUnlockedVideo = document.querySelector('.disabled-video');
            if (nextUnlockedVideo) {
                nextUnlockedVideo.classList.remove('disabled-video');
                questionSection.style.display = 'none';
                questionContainer.innerHTML = '';

                const nextVideoUrl = nextUnlockedVideo.dataset.video;
                currentVideoId = parseInt(nextUnlockedVideo.dataset.id);

                videoSource.src = nextVideoUrl;
                videoPlayer.load();
                videoPlayer.play();
            }
        }

        function getCurrentModulIndexByVideoId(videoId) {
            for (let i = 0; i < komModuls.length; i++) {
                const videoIds = komModuls[i].videos.map(v => v.id);
                if (videoIds.includes(parseInt(videoId))) {
                    return i;
                }
            }
            return -1;
        }

        function isModulCompleted(index) {
            if (index < 0 || !komModuls[index]) return false;
            return komModuls[index].videos.every(v => userVideoProgress.includes(v.id));
        }

        function showModuleCompleteOptions() {
            document.getElementById('moduleCompletePopup').style.display = 'block';

            document.getElementById('btnNextModule').addEventListener('click', function() {
                const nextModulIndex = getCurrentModulIndexByVideoId(currentVideoId) + 1;

                if (komModuls[nextModulIndex]) {
                    const firstVideo = komModuls[nextModulIndex].videos[0];
                    if (firstVideo) {
                        if (!userVideoProgress.includes(firstVideo.id)) {
                            userVideoProgress.push(firstVideo.id);
                        }

                        const newSource = `videos/${firstVideo.videos}`;
                        videoSource.src = newSource;
                        videoPlayer.load();
                        videoPlayer.play();

                        document.querySelectorAll('.video-link').forEach(link => {
                            if (parseInt(link.dataset.id) === firstVideo.id) {
                                link.classList.remove('disabled-video');
                            }
                        });
                    } else {
                        alert("Modul selanjutnya tidak memiliki video.");
                    }
                } else {
                    alert("Semua modul telah selesai 🎉");
                }

                document.getElementById('moduleCompletePopup').style.display = 'none';
            });

            document.getElementById('btnTutorial').addEventListener('click', function() {
                window.location.href = "{{ route('kom.kelas_tutor.index') }}";
                document.getElementById('moduleCompletePopup').style.display = 'none';
            });
        }
    </script>


    <div id="moduleCompletePopup"
        style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:9999;">
        <p><strong>Modul selesai!</strong></p>
        <p>Silakan pilih tindakan berikut:</p>
        <button id="btnNextModule" class="btn btn-success">Lanjut ke Modul Selanjutnya</button>
        <button id="btnTutorial" class="btn btn-primary">Ikut Kelas Tutorial</button>
    </div>

    <!-- <div class="modal fade" id="timeUpModal" tabindex="-1" aria-labelledby="timeUpModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title fs-5" id="timeUpModalLabel">Selamat Datang KOM DIGITAL</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                 
                                Shalom untuk memulai, Modul 1 dan Video 1 anda dapat klik pada samping kanan. <br>
                                Selamat belajar, Tuhan Yesus Memberkati.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div> -->


@stop
