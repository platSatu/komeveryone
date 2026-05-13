@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row">
            {{-- Video Player Section --}}
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 id="videoTitle" class="mb-0">
                                <i class="fas fa-play-circle me-2"></i>
                                Pilih Video untuk Mulai Belajar
                            </h5>
                            <div class="video-controls">
                                {{-- <button class="btn btn-sm btn-outline-light" id="fullscreenBtn" title="Fullscreen">
                                    <i class="fas fa-expand"></i>
                                </button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="videoContainer" class="position-relative">
                            

                            <!-- Video Player -->
                            <!-- <video id="mainVideo" class="w-100" autoplay preload="auto"
                                        style="max-height: 500px; background: #000; width: 100%; display: block;">
                                        <source id="videoSource" src="video.mp4" type="video/mp4">
                                        Browser Anda tidak mendukung video HTML5.
                                    </video> -->
                            <video id="mainVideo" class="w-100" preload="metadata"
    style="max-height: 500px; background: #000; width: 100%; display: block;">
    <source id="videoSource" src="video.mp4" type="video/mp4">
    Browser Anda tidak mendukung video HTML5.
</video>

                            <!-- Kontrol video buatan -->
                            <div class="video-controls"
    style="position: relative; bottom: 10px; text-align: center; background: rgba(0,0,0,0.5); padding: 10px; color: white;">
    <button id="playPauseBtn" aria-label="Play/Pause Video" 
        title="Play/Pause"
        style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; margin-right: 10px;">
        ▶
    </button>
    <label for="speedControl" style="padding-top: 6px; margin-right: 5px;">Kecepatan:</label>
    <select id="speedControl" aria-label="Pengaturan Kecepatan Video" 
        style="background: rgba(255,255,255,0.8); border: 1px solid #ccc; padding: 2px; border-radius: 4px;">
        <option value="0.5">0.5x</option>
        <option value="1" selected>1x (Normal)</option>
        <option value="1.5">1.5x</option>
        <option value="2">2x</option>
    </select>
</div>

                            <!-- JavaScript -->

                            {{-- Loading Overlay --}}
                            <div id="loadingOverlay"
                                class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75"
                                style="display: none !important;">
                                <div class="text-center text-white">
                                    <div class="spinner-border mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    </div>
                                    <div class="h5">Memuat video...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question Section --}}
                <div id="questionSection" class="card mt-4 shadow-lg border-0" style="display: none;">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Pertanyaan Evaluasi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="questionForm">
                            @csrf
                            <input type="hidden" id="questionId" name="question_id">
                            <input type="hidden" id="questionVideoId" name="video_id">

                            <div id="questionContent" class="mb-4"></div>
                            <div id="answersContainer" class="mb-4"></div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>&nbsp;&nbsp;
                                    Kirim Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar Video List --}}
            <div class="col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-dark text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>&nbsp;&nbsp;
                            Video Lecture
                        </h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 650px; overflow-y: auto;">
                        @foreach ($moduls as $modulIndex => $modul)
                            <div class="module-section border-bottom" data-module-id="{{ $modul->id }}">
                                {{-- Module Header --}}
                                <div class="module-header p-4"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="d-flex align-items-center justify-content-between text-white">
                                        <div class="d-flex align-items-center">
                                            <div class="module-icon me-3">
                                                <i class="fas fa-folder-open fa-lg"></i>&nbsp;&nbsp;
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ $modul->name }}</strong>
                                                <small class="opacity-75">Modul {{ $modul->order }}</small>
                                            </div>
                                        </div>
                                        @php
                                            // ✅ FIXED: Calculate module progress based on database
                                            $moduleCompletedVideos = 0;
                                            foreach ($modul->videos as $v) {
                                                $vProgress = $userVideoProgress[$v->id] ?? null;
                                                if (
                                                    $vProgress &&
                                                    $vProgress->status === 'completed' &&
                                                    $vProgress->answered_question
                                                ) {
                                                    $moduleCompletedVideos++;
                                                }
                                            }
                                            $moduleProgress =
                                                $modul->videos->count() > 0
                                                ? round(($moduleCompletedVideos / $modul->videos->count()) * 100)
                                                : 0;
                                            $moduleCompleted = $moduleCompletedVideos === $modul->videos->count();
                                        @endphp
                                        <div class="text-center">
                                            <span
                                                class="badge {{ $moduleCompleted ? 'bg-success' : 'bg-warning' }} mb-2 module-progress">
                                                {{ $moduleCompletedVideos }}/{{ $modul->videos->count() }}
                                            </span>
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-light" style="width: {{ $moduleProgress }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Videos List --}}
                                <div class="videos-list">
                                    @foreach ($modul->videos as $videoIndex => $video)
                                        @php
                                            // ✅ FIXED: Get fresh progress from database
                                            // ✅ FIXED: Unlock logic berdasarkan database history
                                            $progress = $userVideoProgress[$video->id] ?? null;
                                            $isCompleted =
                                                $progress &&
                                                $progress->status === 'completed' &&
                                                $progress->answered_question;

                                            // ✅ FIXED: Smart unlock logic
                                            $isUnlocked = false;

                                            if ($video->sort_video == 1) {
                                                // Video pertama di setiap modul selalu unlocked
                                                $isUnlocked = true;
                                            } else {
                                                // Video selanjutnya: cek video sebelumnya completed
                                                $prevVideo = $modul->videos
                                                    ->where('sort_video', $video->sort_video - 1)
                                                    ->first();
                                                if ($prevVideo) {
                                                    $prevProgress = $userVideoProgress[$prevVideo->id] ?? null;
                                                    $isUnlocked =
                                                        $prevProgress &&
                                                        $prevProgress->status === 'completed' &&
                                                        $prevProgress->answered_question;
                                                }
                                            }

                                            // ✅ FIXED: For modules after first, check if previous module is completed
                                            if ($modul->order > 1 && $video->sort_video == 1) {
                                                $prevModul = $moduls->where('order', $modul->order - 1)->first();
                                                if ($prevModul) {
                                                    $prevModulCompleted = true;
                                                    foreach ($prevModul->videos as $prevModulVideo) {
                                                        $prevModulVideoProgress =
                                                            $userVideoProgress[$prevModulVideo->id] ?? null;
                                                        if (
                                                            !$prevModulVideoProgress ||
                                                            $prevModulVideoProgress->status !== 'completed' ||
                                                            !$prevModulVideoProgress->answered_question
                                                        ) {
                                                            $prevModulCompleted = false;
                                                            break;
                                                        }
                                                    }
                                                    $isUnlocked = $prevModulCompleted;
                                                } else {
                                                    $isUnlocked = false;
                                                }
                                            }

                                            $statusClass = $isCompleted
                                                ? 'completed'
                                                : ($isUnlocked
                                                    ? 'unlocked'
                                                    : 'locked');
                                            $iconClass = $isCompleted
                                                ? 'fa-check-circle text-success'
                                                : ($isUnlocked
                                                    ? 'fa-play-circle text-primary'
                                                    : 'fa-lock text-muted');

                                            // ✅ FIXED: Set active video based on last watched
                                            $isActive = false;
                                            if (
                                                $lastWatchedVideo &&
                                                $lastWatchedVideo->video_id === $video->id &&
                                                $isUnlocked
                                            ) {
                                                $isActive = true;
                                            } elseif (
                                                !$lastWatchedVideo &&
                                                $modul->order == 1 &&
                                                $video->sort_video == 1
                                            ) {
                                                $isActive = true;
                                            }

                                            // ✅ FIXED: Calculate video progress percentage with proper type casting
                                            $videoProgressPercent = 0;
                                            if ($progress && $progress->last_time > 0 && $video->durations) {
                                                $videoDuration = (int) $video->durations;
                                                $lastTime = (int) $progress->last_time;
                                                if ($videoDuration > 0) {
                                                    $videoProgressPercent = min(
                                                        100,
                                                        round(($lastTime / $videoDuration) * 100),
                                                    );
                                                }
                                            }
                                        @endphp

                                        <div class="video-item {{ $statusClass }} {{ $isActive ? 'active' : '' }}"
                                            data-video-id="{{ $video->id }}" data-video-url="{{ asset('videos/' . $video->video) }}"
                                            data-video-title="{{ $video->name }}" data-module-name="{{ $modul->name }}"
                                            data-module-id="{{ $modul->id }}" data-module-order="{{ $modul->order }}"
                                            data-video-sort="{{ $video->sort_video }}"
                                            data-is-unlocked="{{ $isUnlocked ? 'true' : 'false' }}"
                                            data-is-completed="{{ $isCompleted ? 'true' : 'false' }}"
                                            data-has-answered="{{ $progress && $progress->answered_question ? 'true' : 'false' }}"
                                            data-last-time="{{ $progress->last_time ?? 0 }}">

                                            <a href="#"
                                                class="video-link d-block p-3 text-decoration-none position-relative {{ !$isUnlocked ? 'disabled' : '' }}"
                                                style="{{ !$isUnlocked ? 'pointer-events: none; opacity: 0.6;' : '' }}">

                                                {{-- Video Progress Bar --}}
                                                @if ($videoProgressPercent > 0 && !$isCompleted)
                                                    <div class="video-progress-bar position-absolute top-0 start-0 bg-warning"
                                                        style="height: 3px; width: {{ $videoProgressPercent }}%; z-index: 1;">
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center">
                                                    {{-- Video Icon --}}
                                                    <div class="video-icon me-3 d-flex align-items-center justify-content-center mr-3"
                                                        style="width: 40px; height: 40px; border-radius: 50%; 
                                                                                    background: {{ $isCompleted ? '#28a745' : ($isUnlocked ? '#007bff' : '#6c757d') }}20;">
                                                        <i class="fas {{ $iconClass }}"></i>
                                                    </div>

                                                    {{-- Video Info --}}
                                                    <div class="flex-grow-1">
                                                        <div class="video-title fw-bold text-dark mb-1">
                                                            {{ $video->name }}
                                                        </div>
                                                        <div class="video-meta d-flex align-items-center">
                                                            <small class="text-muted me-3">
                                                                @if ($isCompleted)
                                                                    <i
                                                                        class="fas fa-check-circle text-success me-1"></i>&nbsp;&nbsp;Selesai
                                                                @elseif($progress && $progress->last_time > 0)
                                                                    <i
                                                                        class="fas fa-clock text-warning me-1"></i>&nbsp;&nbsp;{{ gmdate('i:s', (int) $progress->last_time) }}
                                                                @elseif($isUnlocked)
                                                                    <i class="fas fa-play text-primary me-1"></i>&nbsp;&nbsp;Siap
                                                                    ditonton
                                                                @else
                                                                    <i class="fas fa-lock text-muted me-1"></i>&nbsp;&nbsp;Terkunci
                                                                @endif
                                                            </small>
                                                            @if ($video->durations && (int) $video->durations > 0)
                                                                <small class="text-muted">
                                                                    <i
                                                                        class="fas fa-clock me-1"></i>&nbsp;&nbsp;{{ gmdate('i:s', (int) $video->durations) }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Status Badge --}}
                                                    <div class="video-status">
                                                        @if ($isCompleted)
                                                            <span class="badge bg-success rounded-pill">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @elseif($progress && $progress->last_time > 0)
                                                            <span class="badge bg-warning rounded-pill">
                                                                {{ $videoProgressPercent }}%
                                                            </span>
                                                        @elseif($isUnlocked)
                                                            <span class="badge bg-primary rounded-pill">
                                                                <i class="fas fa-play"></i>
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary rounded-pill">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Progress Summary --}}
                <div class="card mt-4 shadow-lg border-0">
                    <div class="card-header bg-gradient-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Progress Pembelajaran
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $totalVideos = $moduls->sum(fn($m) => $m->videos->count());
                            $completedVideos = 0;
                            foreach ($moduls as $modul) {
                                foreach ($modul->videos as $video) {
                                    $vProgress = $userVideoProgress[$video->id] ?? null;
                                    if (
                                        $vProgress &&
                                        $vProgress->status === 'completed' &&
                                        $vProgress->answered_question
                                    ) {
                                        $completedVideos++;
                                    }
                                }
                            }
                            $progressPercentage = $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0;
                        @endphp

                        <div class="text-center">
                            <div class="progress mb-3" style="height: 12px;">
                                <div class="progress-bar bg-gradient-success progress-bar-striped progress-bar-animated"
                                    id="overallProgress" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <h3 id="progressText" class="text-primary mb-2">{{ $progressPercentage }}% Selesai</h3>
                            <p class="text-muted mb-3">
                                <span id="completedCount">{{ $completedVideos }}</span> dari
                                <span id="totalCount">{{ $totalVideos }}</span> video selesai
                            </p>

                            {{-- Achievement Badges --}}
                            {{-- <div class="achievement-badges">
                                @if ($progressPercentage >= 25)
                                <span class="badge bg-bronze me-1 mb-1">
                                    <i class="fas fa-medal me-1"></i>Pemula
                                </span>
                                @endif
                                @if ($progressPercentage >= 50)
                                <span class="badge bg-silver me-1 mb-1">
                                    <i class="fas fa-trophy me-1"></i>Menengah
                                </span>
                                @endif
                                @if ($progressPercentage >= 75)
                                <span class="badge bg-gold me-1 mb-1">
                                    <i class="fas fa-crown me-1"></i>Mahir
                                </span>
                                @endif
                                @if ($progressPercentage >= 100)
                                <span class="badge bg-diamond me-1 mb-1">
                                    <i class="fas fa-gem me-1"></i>Expert
                                </span>
                                @endif
                            </div> --}}

                            {{-- Certificate Button --}}
                            @if ($progressPercentage >= 100)
                                <div class="mt-3">
                                    <button class="btn btn-warning btn-sm" id="certificateBtn">
                                        <i class="fas fa-certificate me-2"></i>
                                        Dapatkan Sertifikat
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Styles --}}
    <style>
        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%) !important;
        }

        /* Video item styles */
        .video-item {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .video-item:last-child {
            border-bottom: none;
        }

        .video-item.completed .video-link {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 4px solid #28a745;
        }

        .video-item.unlocked .video-link:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            transform: translateX(3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .video-item.active .video-link {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #ffc107;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .video-item.locked .video-link {
            background: #f8f9fa;
            cursor: not-allowed;
        }

        /* Video progress bar */
        .video-progress-bar {
            transition: width 0.3s ease;
        }

        /* Module section */
        .module-section:last-child {
            border-bottom: none !important;
        }

        .module-header {
            position: relative;
            overflow: hidden;
        }

        .module-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .module-header:hover::before {
            opacity: 1;
        }

        /* Video icon animations */
        .video-icon {
            transition: all 0.3s ease;
        }

        .video-item:hover .video-icon {
            transform: scale(1.1);
        }

        /* Achievement badges */
        .bg-bronze {
            background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%) !important;
            color: white !important;
        }

        .bg-silver {
            background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%) !important;
            color: white !important;
        }

        .bg-gold {
            background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%) !important;
            color: #333 !important;
        }

        .bg-diamond {
            background: linear-gradient(135deg, #b9f2ff 0%, #00d4ff 100%) !important;
            color: white !important;
        }

        /* Card enhancements */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            border: none;
        }

        /* Progress bar animations */
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        /* Scrollbar styling */
        .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* Question form enhancements */
        .answer-option {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .answer-option:hover {
            background-color: #f8f9fa !important;
            border-color: #007bff !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .answer-option.selected {
            background-color: #e3f2fd !important;
            border-color: #007bff !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 1rem;
            }

            .video-item .video-link {
                padding: 0.75rem;
            }

            #mainVideo {
                max-height: 250px;
            }

            .module-header {
                padding: 1rem !important;
            }

            .video-icon {
                width: 35px !important;
                height: 35px !important;
            }
        }

        /* Loading animation */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Certificate button animation */
        #certificateBtn {
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 5px #ffc107, 0 0 10px #ffc107, 0 0 15px #ffc107;
            }

            to {
                box-shadow: 0 0 10px #ffc107, 0 0 20px #ffc107, 0 0 30px #ffc107;
            }
        }
    </style>

    {{-- JavaScript Configuration --}}

    <script>
        // Global Configuration
        window.videoLearningConfig = {
            csrfToken: '{{ csrf_token() }}',
            routes: {
                saveWatchTime: '{{ route('kom.kelas_lecture.video.saveWatchTime') }}',
                getQuestions: '{{ url('kom-digital/kelas/kom-100/get-video-questions') }}',
                submitAnswer: '{{ route('kom.kelas_lecture.submit_answer') }}',
                getNextVideo: '{{ url('kom-digital/kelas/kom-100/get-next-video') }}',
                getUserStats: '{{ url('kom-digital/kelas/kom-100/get-user-stats') }}',
                kelasTutorial: '{{ url('/kom-digital/kom-100/kelas-tutorial') }}'
            },
            user: {
                id: '{{ auth()->id() }}',
                lastVideoId: '{{ $lastWatchedVideo->video_id ?? null }}'
            },
            debug: {{ config('app.debug') ? 'true' : 'false' }}
                };

        console.log('🔧 Video Learning Config loaded:', window.videoLearningConfig);
    </script>

    <script>
        class VideoLearningSystem {
            constructor() {
                this.config = window.videoLearningConfig;
                this.currentVideo = null;
                this.watchTimeInterval = null;

                // Cache DOM elements
                this.elements = {
                    video: document.getElementById('mainVideo'),
                    videoSource: document.getElementById('videoSource'),
                    videoTitle: document.getElementById('videoTitle'),
                    questionSection: document.getElementById('questionSection'),
                    questionForm: document.getElementById('questionForm'),
                    loadingOverlay: document.getElementById('loadingOverlay')
                };

                this.init();
            }

            init() {
                console.log('🚀 Video Learning System initializing...');
                this.setupEventListeners();
                this.loadInitialVideo();
                console.log('✅ Video Learning System initialized');
            }

            setupEventListeners() {
                // Video events
                this.elements.video.addEventListener('loadedmetadata', () => this.onVideoLoaded());
                this.elements.video.addEventListener('timeupdate', () => this.onTimeUpdate());
                this.elements.video.addEventListener('ended', () => this.onVideoEnded());
                this.elements.video.addEventListener('error', (e) => this.onVideoError(e));

                // Video click handlers
                document.querySelectorAll('.video-link').forEach(link => {
                    link.addEventListener('click', (e) => this.onVideoClick(e));
                });

                // Question form
                if (this.elements.questionForm) {
                    this.elements.questionForm.addEventListener('submit', (e) => this.onSubmitAnswer(e));
                }

                console.log('✅ Event listeners setup complete');
            }

            onVideoClick(e) {
                e.preventDefault();

                const videoItem = e.target.closest('.video-item');
                if (!videoItem) return;

                const isUnlocked = videoItem.dataset.isUnlocked === 'true';
                if (!isUnlocked) {
                    this.showAlert('warning', 'Video ini masih terkunci. Selesaikan video sebelumnya terlebih dahulu.');
                    return;
                }

                this.loadVideo(videoItem);
            }

            loadVideo(videoItem) {
                console.log('📹 Loading video:', videoItem.dataset.videoTitle);

                // Update current video data
                this.currentVideo = {
                    id: videoItem.dataset.videoId,
                    title: videoItem.dataset.videoTitle,
                    moduleName: videoItem.dataset.moduleName,
                    moduleId: videoItem.dataset.moduleId,
                    moduleOrder: parseInt(videoItem.dataset.moduleOrder),
                    videoSort: parseInt(videoItem.dataset.videoSort),
                    url: videoItem.dataset.videoUrl,
                    lastTime: parseInt(videoItem.dataset.lastTime) || 0,
                    isCompleted: videoItem.dataset.isCompleted === 'true',
                    hasAnswered: videoItem.dataset.hasAnswered === 'true'
                };

                // Update UI
                this.updateActiveVideo(videoItem);
                this.elements.videoTitle.innerHTML = `
                    <i class="fas fa-play-circle me-2"></i>
                    ${this.currentVideo.moduleName} - ${this.currentVideo.title}
                `;

                // Hide question section
                this.hideQuestion();

                // ✅ FIX 1: Show loading properly
                this.showLoading(true);

                // Load video
                this.elements.videoSource.src = this.currentVideo.url;
                this.elements.video.load();
            }

            onVideoLoaded() {
                console.log('✅ Video loaded successfully');

                // ✅ FIX 1: Hide loading when video is ready
                this.showLoading(false);

                // Restore last position
                if (this.currentVideo.lastTime > 0) {
                    this.elements.video.currentTime = this.currentVideo.lastTime;
                    console.log(`⏰ Restored to ${this.currentVideo.lastTime}s`);
                }

                this.startWatchTimeTracking();
            }

            onTimeUpdate() {
                // Save every 10 seconds
                if (!this.timeUpdateThrottle) {
                    this.timeUpdateThrottle = setTimeout(() => {
                        this.saveWatchTime(false);
                        this.timeUpdateThrottle = null;
                    }, 10000);
                }
            }

            onVideoEnded() {
                console.log('🎬 Video ended');
                this.stopWatchTimeTracking();
                this.saveWatchTime(true);

                // Check if already answered
                if (this.currentVideo.hasAnswered) {
                    console.log('✅ Already answered, proceeding to completion flow');
                    setTimeout(() => {
                        this.handleVideoCompleted();
                    }, 1000);
                    return;
                }

                // Load questions
                console.log('❓ Loading questions...');
                setTimeout(() => {
                    this.loadQuestions();
                }, 500);
            }

            async loadQuestions() {
                if (!this.currentVideo?.id) {
                    console.error('❌ No current video ID');
                    return;
                }

                console.log('❓ Loading questions for video:', this.currentVideo.id);

                try {
                    const url = `${this.config.routes.getQuestions}/${this.currentVideo.id}`;
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('📥 Questions data:', data);

                    if (data.has_answered) {
                        this.showAlert('info', 'Anda sudah menjawab pertanyaan ini.');
                        setTimeout(() => this.handleVideoCompleted(), 2000);
                        return;
                    }

                    if (!data.has_questions || !data.questions?.length) {
                        this.showAlert('info', 'Video ini tidak memiliki pertanyaan.');
                        setTimeout(() => this.handleVideoCompleted(), 2000);
                        return;
                    }

                    // Display question
                    this.displayQuestions(data.questions);
                    //this.displayQuestion(data.questions[0]);

                } catch (error) {
                    console.error('❌ Load questions error:', error);
                    this.showAlert('error', 'Gagal memuat pertanyaan. Melanjutkan ke video berikutnya...');
                    setTimeout(() => this.handleVideoCompleted(), 3000);
                }
            }
            displayQuestions(questions) {
                console.log('📝 Displaying all questions:', questions);

                if (!questions?.length) {
                    console.error('❌ No questions to display');
                    return;
                }

                // Kosongkan kontainer pertanyaan dan jawaban
                const questionContent = document.getElementById('questionContent');
                const answersContainer = document.getElementById('answersContainer');

                questionContent.innerHTML = '';
                answersContainer.innerHTML = '';

                // Buat elemen untuk setiap pertanyaan dan jawabannya
                questions.forEach((question, qIndex) => {
                    // Buat container pertanyaan
                    const questionHtml = document.createElement('div');
                    questionHtml.classList.add('mb-4');

                    questionHtml.innerHTML = `
                    <div class="alert alert-primary border-0 shadow-sm">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-question-circle me-2"></i>
                            ${qIndex + 1}. ${question.question}
                        </h5>
                    </div>
                `;

                    // Buat container jawaban untuk pertanyaan ini
                    const answersHtml = question.answers.map((answer, index) => `
                    <div class="form-check mb-2 p-3 border rounded-3 answer-option" data-answer-id="${answer.id}">
                        <input class="form-check-input" type="radio" 
                               name="answer_${question.id}" id="answer_${question.id}_${answer.id}" value="${answer.id}" required>
                        <label class="form-check-label w-100" for="answer_${question.id}_${answer.id}">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-3 text-white mr-3">${String.fromCharCode(65 + index)}</span>
                                <span>${answer.answer}</span>
                            </div>
                        </label>
                    </div>
                `).join('');

                    const answersContainerDiv = document.createElement('div');
                    answersContainerDiv.innerHTML = answersHtml;

                    questionHtml.appendChild(answersContainerDiv);
                    questionContent.appendChild(questionHtml);
                });

                // Simpan semua question IDs di form (bisa disimpan sebagai input hidden JSON)
                const questionIdsInput = document.getElementById('questionIds');
                if (questionIdsInput) {
                    questionIdsInput.value = JSON.stringify(questions.map(q => q.id));
                } else {
                    // Jika belum ada, buat input hidden baru
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.id = 'questionIds';
                    input.name = 'question_ids';
                    input.value = JSON.stringify(questions.map(q => q.id));
                    this.elements.questionForm.appendChild(input);
                }

                // Setup answer interactions untuk semua jawaban
                this.setupAnswerInteractions();

                // Tampilkan section pertanyaan
                this.showQuestion();
            }

            displayQuestion(question) {
                console.log('📝 Displaying question:', question.question);

                if (!question.answers?.length) {
                    console.error('❌ No answers found');
                    return;
                }

                // Set question data
                document.getElementById('questionId').value = question.id;
                document.getElementById('questionVideoId').value = this.currentVideo.id;

                // Set question content
                document.getElementById('questionContent').innerHTML = `
                    <div class="alert alert-primary border-0 shadow-sm">
                        <h5 class="text-primary mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            ${question.question}
                        </h5>
                    </div>
                `;

                // Set answers
                const answersHtml = question.answers.map((answer, index) => `
                    <div class="form-check mb-3 p-3 border rounded-3 answer-option" data-answer-id="${answer.id}">
                        <input class="form-check-input" type="radio" name="answer" 
                               id="answer_${answer.id}" value="${answer.id}" required>
                        <label class="form-check-label w-100" for="answer_${answer.id}">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-3">${String.fromCharCode(65 + index)}</span>
                                <span>${answer.answer}</span>
                            </div>
                        </label>
                    </div>
                `).join('');

                document.getElementById('answersContainer').innerHTML = answersHtml;

                // Setup answer interactions
                this.setupAnswerInteractions();

                // Show question section
                this.showQuestion();
            }

            setupAnswerInteractions() {
                document.querySelectorAll('.answer-option').forEach(option => {
                    const radio = option.querySelector('input[type="radio"]');

                    option.addEventListener('click', function () {
                        // Remove selected from all
                        document.querySelectorAll('.answer-option').forEach(opt => {
                            opt.classList.remove('selected');
                            opt.style.backgroundColor = '';
                            opt.style.borderColor = '#dee2e6';
                        });

                        // Add selected to clicked
                        this.classList.add('selected');
                        this.style.backgroundColor = '#e3f2fd';
                        this.style.borderColor = '#007bff';
                        radio.checked = true;
                    });
                });
            }

            async onSubmitAnswer(e) {
                e.preventDefault();

                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                submitBtn.disabled = true;

                try {
                    const formData = new FormData(this.elements.questionForm);

                    // Ambil semua question IDs
                    const questionIds = JSON.parse(formData.get('question_ids') || '[]');

                    // Buat payload jawaban semua pertanyaan
                    const answersPayload = questionIds.map(qid => ({
                        question_id: qid,
                        answer_id: formData.get(`answer_${qid}`)
                    }));

                    // Kirim data ke backend
                    const response = await fetch(this.config.routes.submitAnswer, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            video_id: this.currentVideo.id,
                            answers: answersPayload
                        })
                    });

                    const data = await response.json();
                    console.log('📥 Submit response:', data);

                    if (data.success) {
                        await this.showAlert('success', data.message || '🎉 Selamat! Jawaban Anda benar!');

                        this.hideQuestion();
                        this.markVideoCompleted();

                        setTimeout(() => {
                            if (data.module_completed) {
                                this.handleModuleCompleted();
                            } else {
                                this.handleVideoCompleted();
                            }
                        }, 1000);

                    } else {
                        await this.showAlert('error', data.message || '❌ Jawaban Anda salah! Silakan coba lagi.');
                        this.resetAnswerSelection();
                    }

                } catch (error) {
                    console.error('❌ Submit error:', error);
                    await this.showAlert('error', 'Gagal mengirim jawaban: ' + error.message);
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }


            // ✅ FIX 1: Add resetAnswerSelection method
            resetAnswerSelection() {
                document.querySelectorAll('.answer-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.style.backgroundColor = '';
                    opt.style.borderColor = '#dee2e6';
                });

                const checkedRadio = this.elements.questionForm.querySelector('input[name="answer"]:checked');
                if (checkedRadio) {
                    checkedRadio.checked = false;
                }

                console.log('🔄 Answer selection reset');
            }

            handleVideoCompleted() {
                console.log('🎯 handleVideoCompleted() called');

                const nextVideo = this.findNextVideo();

                if (nextVideo) {
                    this.showAlert('question', `Lanjut ke video berikutnya: "${nextVideo.dataset.videoTitle}"?`, true, {
                        confirmButtonText: '<i class="fas fa-forward me-2"></i>&nbsp;&nbsp;Lanjut Video',
                        cancelButtonText: 'Nanti Saja',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.unlockVideo(nextVideo);
                            this.loadVideo(nextVideo);
                        }
                    });
                } else {
                    // ✅ FIX 2: Check if this was the last video in a module
                    const isLastVideoInModule = this.isLastVideoInCurrentModule();

                    if (isLastVideoInModule) {
                        console.log('🏆 Last video in module completed');
                        this.handleModuleCompleted();
                    } else {
                        this.showAlert('success', '🎉 Selamat! Anda telah menyelesaikan semua video!');
                    }
                }
            }

            // ✅ FIX 2: Enhanced module completion handler
            handleModuleCompleted() {
                console.log('🏆 handleModuleCompleted() called');

                const hasNextModule = this.hasNextModule();
                const isAllModulesCompleted = this.isAllModulesCompleted();

                console.log('Module completion status:', {
                    hasNextModule,
                    isAllModulesCompleted,
                    currentModule: this.currentVideo.moduleName
                });

                let alertConfig = {
                    title: '🏆 Modul Selesai!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-trophy fa-4x text-warning mb-4"></i>
                            <h4 class="text-success mb-3">Selamat!</h4>
                            <p class="mb-3">Anda telah menyelesaikan modul <strong>"${this.currentVideo.moduleName}"</strong>!</p>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>&nbsp;&nbsp;
                                Semua video dan pertanyaan telah diselesaikan!
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-coffee me-2"></i>&nbsp;&nbsp;Istirahat Dulu',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false
                };

                if (isAllModulesCompleted) {
                    // All modules completed
                    alertConfig.title = '🎓 Semua Modul Selesai!';
                    alertConfig.html = `
                        <div class="text-center">
                            <i class="fas fa-crown fa-4x text-warning mb-4"></i>
                            <h4 class="text-success mb-3">Luar Biasa!</h4>
                            <p class="mb-3">Anda telah menyelesaikan <strong>seluruh kurikulum pembelajaran</strong>!</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-certificate me-2"></i>
                                Anda berhak mendapatkan sertifikat!
                            </div>
                        </div>
                    `;
                    alertConfig.confirmButtonText = '<i class="fas fa-certificate me-2"></i>Dapatkan Sertifikat';
                    alertConfig.denyButtonText =
                        '<i class="fas fa-graduation-cap me-2"></i>&nbsp;&nbsp;Ikut Kelas Tutorial';
                    alertConfig.showDenyButton = true;
                    alertConfig.confirmButtonColor = '#ffc107';
                    alertConfig.denyButtonColor = '#17a2b8';
                } else if (hasNextModule) {
                    // Has next module
                    alertConfig.confirmButtonText =
                        '<i class="fas fa-forward me-2"></i>&nbsp;&nbsp;Lanjut Modul Berikutnya';
                    alertConfig.denyButtonText =
                        '<i class="fas fa-graduation-cap me-2"></i>&nbsp;&nbsp;Ikut Kelas Tutorial';
                    alertConfig.showDenyButton = true;
                    alertConfig.confirmButtonColor = '#28a745';
                    alertConfig.denyButtonColor = '#17a2b8';
                } else {
                    // No next module but not all completed (edge case)
                    alertConfig.confirmButtonText =
                        '<i class="fas fa-graduation-cap me-2"></i>&nbsp;&nbsp;Ikut Kelas Tutorial';
                    alertConfig.confirmButtonColor = '#17a2b8';
                }

                this.showAlert('success', '', false, alertConfig).then((result) => {
                    console.log('Module completion choice:', result);

                    if (result.isConfirmed) {
                        if (isAllModulesCompleted) {
                            // Download certificate
                            this.handleCertificateDownload();
                        } else if (hasNextModule) {
                            // Go to next module
                            const nextVideo = this.findNextVideo();
                            if (nextVideo) {
                                this.showAlert('info', 'Membuka modul berikutnya...', false, {
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                setTimeout(() => {
                                    this.unlockVideo(nextVideo);
                                    this.loadVideo(nextVideo);
                                }, 4000);
                            } else {
                                this.showAlert('error', 'Modul berikutnya tidak ditemukan.');
                            }
                        } else {
                            // Go to tutorial
                            this.redirectToTutorial();
                        }
                    } else if (result.isDenied) {
                        // Go to tutorial
                        this.redirectToTutorial();
                    }
                    // If cancelled, do nothing
                });
            }

            // ✅ FIX 2: Helper methods for module completion logic
            isLastVideoInCurrentModule() {
                if (!this.currentVideo) return false;

                const currentModuleSection = document.querySelector(`[data-module-id="${this.currentVideo.moduleId}"]`);
                if (!currentModuleSection) return false;

                const allVideosInModule = currentModuleSection.querySelectorAll('.video-item');
                const lastVideoInModule = Array.from(allVideosInModule).pop();

                return lastVideoInModule && lastVideoInModule.dataset.videoId === this.currentVideo.id;
            }

            hasNextModule() {
                const nextModuleOrder = this.currentVideo.moduleOrder + 1;
                const nextModuleSection = document.querySelector(`[data-module-order="${nextModuleOrder}"]`);
                return nextModuleSection !== null;
            }

            isAllModulesCompleted() {
                const allModules = document.querySelectorAll('.module-section');
                let allCompleted = true;

                allModules.forEach(moduleSection => {
                    const totalVideos = moduleSection.querySelectorAll('.video-item').length;
                    const completedVideos = moduleSection.querySelectorAll('.video-item.completed').length;

                    if (completedVideos < totalVideos) {
                        allCompleted = false;
                    }
                });

                return allCompleted;
            }

            handleCertificateDownload() {
                this.showAlert('question', 'Download sertifikat sekarang?', true, {
                    confirmButtonText: '<i class="fas fa-download me-2"></i>Download',
                    cancelButtonText: 'Nanti Saja',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.showAlert('success', 'Sertifikat berhasil didownload!', false, {
                            timer: 3000
                        });

                        // Implement actual certificate download
                        console.log('🎓 Certificate download triggered');
                    }
                });
            }

            redirectToTutorial() {
                this.showAlert('info', 'Mengarahkan ke kelas tutorial...', false, {
                    timer: 2000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href = this.config.routes.kelasTutorial;
                }, 2000);
            }

            findNextVideo() {
                if (!this.currentVideo) return null;

                // Find next video in same module
                const currentModuleSection = document.querySelector(`[data-module-id="${this.currentVideo.moduleId}"]`);
                const nextVideoSort = this.currentVideo.videoSort + 1;
                let nextVideo = currentModuleSection?.querySelector(`[data-video-sort="${nextVideoSort}"]`);

                if (nextVideo) {
                    console.log('🔍 Found next video in same module:', nextVideo.dataset.videoTitle);
                    return nextVideo;
                }

                // Find first video in next module
                const nextModuleOrder = this.currentVideo.moduleOrder + 1;
                const nextModuleSection = document.querySelector(`[data-module-order="${nextModuleOrder}"]`);

                if (nextModuleSection) {
                    nextVideo = nextModuleSection.querySelector('[data-video-sort="1"]');
                    if (nextVideo) {
                        console.log('🔍 Found first video in next module:', nextVideo.dataset.videoTitle);
                        return nextVideo;
                    }
                }

                console.log('🔍 No next video found');
                return null;
            }

            unlockVideo(videoElement) {
                if (!videoElement) return;

                console.log('🔓 Unlocking video:', videoElement.dataset.videoTitle);

                // Update data attributes
                videoElement.dataset.isUnlocked = 'true';
                videoElement.classList.remove('locked');
                videoElement.classList.add('unlocked');

                // Update link
                const link = videoElement.querySelector('.video-link');
                if (link) {
                    link.classList.remove('disabled');
                    link.style.pointerEvents = '';
                    link.style.opacity = '';
                }

                // Update icon
                const icon = videoElement.querySelector('.fas');
                if (icon) {
                    icon.classList.remove('fa-lock', 'text-muted');
                    icon.classList.add('fa-play-circle', 'text-primary');
                }

                // Update status text
                const statusText = videoElement.querySelector('.video-meta small');
                if (statusText) {
                    statusText.innerHTML = '<i class="fas fa-play text-primary me-1"></i>&nbsp;&nbsp;Siap ditonton';
                }

                // Update status badge
                const statusBadge = videoElement.querySelector('.video-status .badge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-primary rounded-pill';
                    statusBadge.innerHTML = '<i class="fas fa-play"></i>';
                }
            }

            markVideoCompleted() {
                const currentItem = document.querySelector(`[data-video-id="${this.currentVideo.id}"]`);
                if (!currentItem) return;

                console.log('✅ Marking video as completed:', this.currentVideo.title);

                // Update data attributes
                currentItem.dataset.isCompleted = 'true';
                currentItem.dataset.hasAnswered = 'true';
                currentItem.classList.add('completed');

                // Update icon
                const icon = currentItem.querySelector('.fas');
                if (icon) {
                    icon.classList.remove('fa-play-circle', 'text-primary');
                    icon.classList.add('fa-check-circle', 'text-success');
                }

                // Update status text
                const statusText = currentItem.querySelector('.video-meta small');
                if (statusText) {
                    statusText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Selesai';
                }

                // Update status badge
                const statusBadge = currentItem.querySelector('.video-status .badge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-success rounded-pill';
                    statusBadge.innerHTML = '<i class="fas fa-check"></i>';
                }

                // Update current video object
                this.currentVideo.isCompleted = true;
                this.currentVideo.hasAnswered = true;

                // Update progress
                this.updateProgress();
            }

            updateActiveVideo(videoItem) {
                // Remove active from all videos
                document.querySelectorAll('.video-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Add active to current video
                videoItem.classList.add('active');
            }

            updateProgress() {
                const totalVideos = document.querySelectorAll('.video-item').length;
                const completedVideos = document.querySelectorAll('.video-item.completed').length;
                const percentage = totalVideos > 0 ? Math.round((completedVideos / totalVideos) * 100) : 0;

                const progressBar = document.getElementById('overallProgress');
                const progressText = document.getElementById('progressText');
                const completedCount = document.getElementById('completedCount');

                if (progressBar) progressBar.style.width = percentage + '%';
                if (progressText) progressText.textContent = percentage + '% Selesai';
                if (completedCount) completedCount.textContent = completedVideos;

                console.log(`📊 Progress updated: ${completedVideos}/${totalVideos} (${percentage}%)`);
            }

            loadInitialVideo() {
                let targetVideo = null;

                // Try last watched video
                if (this.config.user.lastVideoId) {
                    targetVideo = document.querySelector(`[data-video-id="${this.config.user.lastVideoId}"]`);
                    if (targetVideo && targetVideo.dataset.isUnlocked === 'true') {
                        console.log('🎯 Loading last watched video');
                        this.loadVideo(targetVideo);
                        return;
                    }
                }

                // Fallback to first unlocked video
                targetVideo = document.querySelector('.video-item[data-is-unlocked="true"]');
                if (targetVideo) {
                    console.log('🎯 Loading first unlocked video');
                    this.loadVideo(targetVideo);
                } else {
                    console.warn('⚠️ No unlocked videos found');
                }
            }

            startWatchTimeTracking() {
                this.stopWatchTimeTracking();
                this.watchTimeInterval = setInterval(() => {
                    if (!this.elements.video.paused && !this.elements.video.ended) {
                        this.saveWatchTime(false);
                    }
                }, 10000);
            }

            stopWatchTimeTracking() {
                if (this.watchTimeInterval) {
                    clearInterval(this.watchTimeInterval);
                    this.watchTimeInterval = null;
                }
            }

            async saveWatchTime(completed = false) {
                if (!this.currentVideo?.id) return;

                const currentTime = Math.floor(this.elements.video.currentTime);
                if (currentTime === 0) return;

                try {
                    const response = await fetch(this.config.routes.saveWatchTime, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            video_id: this.currentVideo.id,
                            time: currentTime,
                            completed: completed
                        })
                    });

                    if (response.ok) {
                        console.log(`💾 Watch time saved: ${currentTime}s`);
                    }
                } catch (error) {
                    console.error('❌ Save watch time error:', error);
                }
            }

            showQuestion() {
                if (this.elements.questionSection) {
                    this.elements.questionSection.style.display = 'block';
                    this.elements.questionSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }

            hideQuestion() {
                if (this.elements.questionSection) {
                    this.elements.questionSection.style.display = 'none';
                    if (this.elements.questionForm) {
                        this.elements.questionForm.reset();
                    }
                }
            }

            // ✅ FIX 1: Proper loading overlay control
            showLoading(show) {
                if (this.elements.loadingOverlay) {
                    if (show) {
                        this.elements.loadingOverlay.style.display = 'flex';
                        this.elements.loadingOverlay.style.setProperty('display', 'flex', 'important');
                    } else {
                        this.elements.loadingOverlay.style.display = 'none';
                        this.elements.loadingOverlay.style.setProperty('display', 'none', 'important');
                    }
                    console.log(`⏳ Loading ${show ? 'shown' : 'hidden'}`);
                }
            }

            showAlert(type, message, showCancel = false, options = {}) {
                const config = {
                    icon: type,
                    title: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff',
                    ...options
                };

                if (showCancel) {
                    config.showCancelButton = true;
                    config.cancelButtonText = 'Batal';
                    config.cancelButtonColor = '#6c757d';
                }

                if (typeof Swal !== 'undefined') {
                    return Swal.fire(config);
                } else {
                    // Fallback
                    if (showCancel) {
                        return Promise.resolve({
                            isConfirmed: confirm(message)
                        });
                    } else {
                        alert(message);
                        return Promise.resolve({
                            isConfirmed: true
                        });
                    }
                }
            }

            onVideoError(e) {
                console.error('❌ Video error:', e);
                this.showLoading(false);
                this.showAlert('error', 'Terjadi kesalahan saat memuat video. Silakan refresh halaman.');
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            console.log('🚀 Initializing Video Learning System...');

            // Load SweetAlert2 if not available
            if (typeof Swal === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                script.onload = function () {
                    console.log('✅ SweetAlert2 loaded');
                    initializeSystem();
                };
                document.head.appendChild(script);
            } else {
                initializeSystem();
            }

            function initializeSystem() {
                try {
                    window.videoLearningSystem = new VideoLearningSystem();

                    // ✅ Simple debug commands
                    window.debugVideo = {
                        getCurrentVideo: () => window.videoLearningSystem.currentVideo,
                        testModuleCompletion: () => {
                            if (window.videoLearningSystem.currentVideo) {
                                window.videoLearningSystem.handleModuleCompleted();
                            } else {
                                console.log('⚠️ No current video loaded');
                            }
                        },
                        testVideoCompletion: () => {
                            if (window.videoLearningSystem.currentVideo) {
                                window.videoLearningSystem.handleVideoCompleted();
                            } else {
                                console.log('⚠️ No current video loaded');
                            }
                        },
                        forceEndVideo: () => {
                            const video = document.getElementById('mainVideo');
                            if (video && video.duration) {
                                video.currentTime = video.duration;
                                console.log('🧪 Video forced to end');
                            } else {
                                console.log('⚠️ No video loaded');
                            }
                        },
                        resetAnswers: () => {
                            if (window.videoLearningSystem.resetAnswerSelection) {
                                window.videoLearningSystem.resetAnswerSelection();
                                console.log('🔄 Answers reset');
                            }
                        },
                        showModuleStatus: () => {
                            const current = window.videoLearningSystem.currentVideo;
                            if (current) {
                                console.log('📊 Module Status:', {
                                    currentModule: current.moduleName,
                                    moduleOrder: current.moduleOrder,
                                    videoSort: current.videoSort,
                                    isLastInModule: window.videoLearningSystem
                                        .isLastVideoInCurrentModule(),
                                    hasNextModule: window.videoLearningSystem.hasNextModule(),
                                    allModulesCompleted: window.videoLearningSystem
                                        .isAllModulesCompleted()
                                });
                            }
                        }
                    };

                    console.log('🎉 Video Learning System ready!');
                    console.log('🔧 Debug commands:');
                    console.log('  debugVideo.getCurrentVideo() - Get current video');
                    console.log('  debugVideo.testModuleCompletion() - Test module completion');
                    console.log('  debugVideo.testVideoCompletion() - Test video completion');
                    console.log('  debugVideo.forceEndVideo() - Force video to end');
                    console.log('  debugVideo.resetAnswers() - Reset answer selection');
                    console.log('  debugVideo.showModuleStatus() - Show module status');

                } catch (error) {
                    console.error('💥 Failed to initialize:', error);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sistem Error',
                            text: 'Gagal memuat sistem pembelajaran. Silakan refresh halaman.',
                            confirmButtonText: 'Refresh',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert('Gagal memuat sistem pembelajaran. Halaman akan di-refresh.');
                        window.location.reload();
                    }
                }
            }
        });

        // ✅ Enhanced keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            if (!window.videoLearningSystem?.elements.video) return;

            const video = window.videoLearningSystem.elements.video;

            // Don't interfere with form inputs
            if (document.activeElement.tagName === 'INPUT' ||
                document.activeElement.tagName === 'TEXTAREA') {
                return;
            }

            switch (e.code) {
                case 'Space':
                    e.preventDefault();
                    if (video.paused) {
                        video.play().catch(e => console.log('Play prevented:', e));
                    } else {
                        video.pause();
                    }
                    break;

                case 'ArrowLeft':
                    e.preventDefault();
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;

                case 'ArrowRight':
                    e.preventDefault();
                    video.currentTime = Math.min(video.duration, video.currentTime + 10);
                    break;

                case 'KeyM':
                    e.preventDefault();
                    video.muted = !video.muted;
                    break;

                case 'KeyQ':
                    if (e.ctrlKey && window.debugVideo) {
                        e.preventDefault();
                        window.debugVideo.forceEndVideo();
                    }
                    break;
            }
        });

        // Auto-save on page unload
        window.addEventListener('beforeunload', function () {
            if (window.videoLearningSystem?.currentVideo) {
                const currentTime = Math.floor(window.videoLearningSystem.elements.video.currentTime);

                if (currentTime > 0) {
                    const formData = new FormData();
                    formData.append('video_id', window.videoLearningSystem.currentVideo.id);
                    formData.append('time', currentTime);
                    formData.append('completed', 'false');
                    formData.append('_token', window.videoLearningConfig.csrfToken);

                    navigator.sendBeacon(window.videoLearningConfig.routes.saveWatchTime, formData);
                }
            }
        });

        // Handle visibility change
        document.addEventListener('visibilitychange', function () {
            if (window.videoLearningSystem?.elements.video) {
                const video = window.videoLearningSystem.elements.video;

                if (document.hidden) {
                    if (!video.paused) {
                        video.pause();
                        window.videoLearningSystem._wasPlayingBeforeHidden = true;
                    }
                } else {
                    if (window.videoLearningSystem._wasPlayingBeforeHidden) {
                        video.play().catch(e => console.log('Play prevented:', e));
                        window.videoLearningSystem._wasPlayingBeforeHidden = false;
                    }
                }
            }
        });

        console.log('📦 Video Learning System script loaded');
    </script>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Additional CSS for better UX --}}
    <script src="https://vjs.zencdn.net/7.21.1/video.min.js"></script>
    {{--
    <script>
        var player = videojs('mainVideo', {
            playbackRates: [0.5, 1, 1.5, 2]
        });
    </script> --}}
    <script>
        const video = document.getElementById('myVideo');
        const buttons = document.querySelectorAll('#speedControls button');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                // Set playback rate video
                const speed = parseFloat(button.getAttribute('data-speed'));
                video.playbackRate = speed;
                // Update active button style
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    </script>
    <script>
        const video = document.getElementById('mainVideo');
        let lastTime = 0;
        // Update posisi terakhir saat video berjalan
        video.addEventListener('timeupdate', () => {
            lastTime = video.currentTime;
        });
        // Cegah seek mundur atau lompat ke posisi sebelum lastTime
        video.addEventListener('seeking', () => {
            if (video.currentTime < lastTime) {
                video.currentTime = lastTime;
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('mainVideo');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const speedControl = document.getElementById('speedControl');

        // Speed control
        speedControl.addEventListener('change', function () {
            const speed = parseFloat(this.value);
            video.playbackRate = speed;
            console.log('Playback speed changed to:', speed);
        });

        // Update icon saat video mulai
        video.addEventListener('play', function () {
            playPauseBtn.innerHTML = '⏸';
            playPauseBtn.title = 'Pause';
        });

        // Update icon saat video pause
        video.addEventListener('pause', function () {
            playPauseBtn.innerHTML = '▶';
            playPauseBtn.title = 'Play';
        });

        // Reset ke awal saat video selesai (jika tidak loop)
        video.addEventListener('ended', function () {
            video.currentTime = 0;
            if (!video.loop) {
                video.pause();
                playPauseBtn.innerHTML = '▶';
            }
        });

        // Toggle play/pause saat tombol diklik
        playPauseBtn.addEventListener('click', function () {
            if (video.paused || video.ended) {
                video.play().catch(err => {
                    console.error("Gagal memulai video:", err);
                });
            } else {
                video.pause();
            }
        });

        // Optional: auto play video saat dimuat
        video.currentTime = 0;
        video.play().catch(err => {
            console.warn('Autoplay mungkin ditolak oleh browser:', err);
        });
    });
</script>
@endsection