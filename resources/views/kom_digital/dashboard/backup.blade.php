@extends('layouts.kom')

@section('content')
    <style>
        .disabled-video {
            pointer-events: none;
            opacity: 0.5;
            text-decoration: none;
        }
    </style>

    <div class="row">
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    Welcome
                </div>
                <div class="card-body">
                    @if ($komModuls->isNotEmpty())
                        <h4 class="mt-0 header-title">{{ $komModuls->first()->title ?? 'Modul 1' }}</h4>
                        <p class="text-muted mb-4 font-14">{{ $komModuls->first()->description ?? 'Deskripsi singkat mengenai video pertama' }}</p>

                        @php
                            $modul1Videos = $komModuls->first()->videos;
                        @endphp

                        <video id="videoPlayer" width="800" controls>
                            <source src="{{ asset('videos/' . $modul1Videos[0]->videos) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                        <div id="video-questions" class="mt-3">
                            <div id="question-section" class="mt-3">
                                <h5>{{ $modul1Videos[0]->pertanyaan->first()->pertanyaan }}</h5>
                                <form id="questionForm">
                                    @csrf
                                    <input type="hidden" name="video_id" value="{{ $modul1Videos[0]->id }}">
                                    <input type="text" name="answer" class="form-control" placeholder="Jawaban">
                                    <button type="submit" class="btn btn-primary mt-2">Kirim Jawaban</button>
                                </form>
                            </div>
                        </div>

                        <p id="nextModulButton" class="text-center" style="margin-top:50px; display: none;">
                            <a href="{{ route('kom.modul.show', ['id' => $komModuls->get(1)->id]) }}" class="btn btn-success">Modul Berikutnya</a>
                        </p>
                    @else
                        <p>No videos available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Video Tutorial</h4>
                    <p class="text-muted mb-4 font-14">Video Tutorial ini berdurasi 30 menit.</p>

                    <div id="accordion-2" role="tablist">
                        @foreach ($komModuls as $modul)
                            <div class="card shadow-none border mb-3">
                                <div class="card-header" role="tab" id="heading-{{ $modul->id }}">
                                    <h5 class="mb-0 mt-0 font-weight-normal">
                                        <a data-toggle="collapse" href="#collapse-{{ $modul->id }}"
                                           aria-expanded="true" aria-controls="collapse-{{ $modul->id }}">
                                            @if (in_array($modul->id, $userProgress))
                                                <i class="mdi mdi-lock-open-outline"></i> {{ $modul->title }}
                                            @else
                                                <i class="mdi mdi-lock-outline"></i> {{ $modul->title }} (Terkunci)
                                            @endif
                                        </a>
                                    </h5>
                                </div>

                                <div id="collapse-{{ $modul->id }}" class="collapse {{ in_array($modul->id, $userProgress) ? '' : 'disabled' }}"
                                     role="tabpanel" aria-labelledby="heading-{{ $modul->id }}" data-parent="#accordion-2">
                                    <div class="card-body">
                                        @foreach ($modul->videos as $index => $video)
                                            @php
                                                // Unlock video pertama, atau jika video sebelumnya sudah selesai
                                                $isUnlocked = $index === 0 || in_array($modul->videos[$index - 1]->id, $userVideoProgress);
                                            @endphp
                                            <p>
                                                <a href="#"
                                                   class="video-link {{ $isUnlocked ? '' : 'disabled-video' }}"
                                                   data-video="{{ $isUnlocked ? asset('videos/' . $video->videos) : '#' }}">
                                                    {{ $video->name }}
                                                    @if (!$isUnlocked)
                                                        <i class="mdi mdi-lock-outline ml-2"></i>
                                                    @endif
                                                </a>
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let currentVideoIndex = 0;
        let totalVideos = {{ count($modul1Videos) }};
        const modulVideos = @json($modul1Videos);

        document.getElementById('questionForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const answer = e.target.answer.value;
            const videoId = e.target.video_id.value;

            fetch("{{ route('kom.validate.answer') }}", {
                method: 'POST',
                body: JSON.stringify({answer: answer, video_id: videoId}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Jawaban benar! Video berikutnya akan dibuka.');

                    currentVideoIndex++;
                    if (currentVideoIndex < totalVideos) {
                        const nextVideoSource = '{{ asset('videos') }}/' + modulVideos[currentVideoIndex].videos;
                        document.getElementById('videoPlayer').querySelector('source').setAttribute('src', nextVideoSource);
                        document.getElementById('videoPlayer').load();
                        document.getElementById('videoPlayer').play();

                        // Aktifkan sidebar link
                        const videoLinks = document.querySelectorAll('.video-link');
                        if (videoLinks[currentVideoIndex]) {
                            videoLinks[currentVideoIndex].classList.remove('disabled-video');
                            videoLinks[currentVideoIndex].style.pointerEvents = 'auto';
                            videoLinks[currentVideoIndex].style.opacity = 1;
                        }
                    }

                    // Tampilkan tombol next modul jika selesai semua
                    if (currentVideoIndex >= totalVideos - 1) {
                        document.getElementById('nextModulButton').style.display = 'block';
                    }
                } else {
                    alert('Jawaban salah, coba lagi.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
@stop
