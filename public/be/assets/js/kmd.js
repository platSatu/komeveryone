/**
 * Video Learning System - Enhanced with Debugging
 * Fixed: Video not showing, title not changing, click not working
 */

class VideoLearningSystem {
    constructor() {
        console.log('🚀 VideoLearningSystem constructor started');
        
        // Validate config first
        if (!window.appConfig) {
            console.error('❌ window.appConfig not found!');
            this.showFallbackError('Konfigurasi sistem tidak ditemukan. Silakan refresh halaman.');
            return;
        }
        
        this.config = window.appConfig;
        this.currentVideo = null;
        this.isProcessing = false;
        this.watchTimeInterval = null;
        this.debounceTimer = null;
        
        // Enhanced DOM element caching with validation
        this.elements = this.cacheElements();
        
        // Validate required elements
        if (!this.validateElements()) {
            console.error('❌ Required elements validation failed');
            return;
        }
        
        this.init();
    }

    cacheElements() {
        console.log('🔍 Caching DOM elements...');
        
        const elements = {
            video: document.getElementById('video'),
            videoSource: document.getElementById('videoSource'),
            videoTitle: document.getElementById('videoTitle'),
            questionSection: document.getElementById('questionSection'),
            questionForm: document.getElementById('questionForm'),
            questionContent: document.getElementById('questionContent'),
            answers: document.getElementById('answers'),
            questionId: document.getElementById('questionId'),
            questionVideoId: document.getElementById('questionVideoId'),
            loadingIndicator: document.getElementById('loadingIndicator'),
            overallProgress: document.getElementById('overallProgress'),
            completedCount: document.getElementById('completedCount'),
            totalCount: document.getElementById('totalCount'),
            progressPercentage: document.getElementById('progressPercentage'),
            progressText: document.getElementById('progressText')
        };
        
        // Log which elements were found
        Object.keys(elements).forEach(key => {
            if (elements[key]) {
                console.log(`✅ Found element: ${key}`);
            } else {
                console.warn(`⚠️ Missing element: ${key}`);
            }
        });
        
        return elements;
    }

    validateElements() {
        const required = ['video', 'videoSource', 'videoTitle'];
        const missing = required.filter(key => !this.elements[key]);
        
        if (missing.length > 0) {
            console.error('❌ Missing required elements:', missing);
            this.showFallbackError(`Element tidak ditemukan: ${missing.join(', ')}`);
            return false;
        }
        
        console.log('✅ All required elements found');
        return true;
    }

    init() {
        console.log('🎯 Initializing Video Learning System...');
        
        try {
            this.setupEventListeners();
            this.updateProgressSummary();
            this.loadInitialVideo();
            
            console.log('✅ Video Learning System initialized successfully');
        } catch (error) {
            console.error('❌ Initialization failed:', error);
            this.showFallbackError('Gagal menginisialisasi sistem video.');
        }
    }

    setupEventListeners() {
        console.log('🔗 Setting up event listeners...');
        
        try {
            // Video events with error handling
            this.elements.video.addEventListener('loadedmetadata', () => {
                console.log('📹 Video metadata loaded');
                this.onVideoLoaded();
            });
            
            this.elements.video.addEventListener('timeupdate', () => this.onTimeUpdate());
            this.elements.video.addEventListener('pause', () => this.onVideoPause());
            this.elements.video.addEventListener('ended', () => this.onVideoEnded());
            this.elements.video.addEventListener('error', (e) => this.onVideoError(e));

            // Question form with null check
            if (this.elements.questionForm) {
                this.elements.questionForm.addEventListener('submit', (e) => this.onSubmitAnswer(e));
            }

            // Enhanced video click handling
            this.setupVideoClickHandlers();

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => this.onKeyPress(e));

            // Page visibility
            document.addEventListener('visibilitychange', () => this.onVisibilityChange());
            
            console.log('✅ Event listeners setup complete');
        } catch (error) {
            console.error('❌ Event listener setup failed:', error);
        }
    }

    setupVideoClickHandlers() {
        console.log('🖱️ Setting up video click handlers...');
        
        const videoLinks = document.querySelectorAll('.video-link');
        console.log(`📋 Found ${videoLinks.length} video links`);
        
        if (videoLinks.length === 0) {
            console.warn('⚠️ No video links found!');
            return;
        }

        videoLinks.forEach((link, index) => {
            const videoItem = link.closest('.video-item');
            if (videoItem) {
                console.log(`🎬 Setting up click handler for video ${index + 1}:`, videoItem.dataset.videoTitle);
            }
            
            link.addEventListener('click', (e) => {
                console.log('🖱️ Video link clicked:', e.target);
                this.onVideoClick(e);
            });
        });
        
        console.log('✅ Video click handlers setup complete');
    }

    loadInitialVideo() {
        console.log('🎯 Loading initial video...');
        
        try {
            // Debug current config
            console.log('🔍 User config:', this.config.user);
            
            let targetVideoId = this.config.user.currentVideoId;
            let targetElement = null;

            console.log('🎯 Target video ID from config:', targetVideoId);

            // Try to find video by ID
            if (targetVideoId) {
                targetElement = document.querySelector(`[data-video-id="${targetVideoId}"]`);
                console.log('🔍 Found target element by ID:', targetElement ? 'Yes' : 'No');
                
                if (targetElement) {
                    console.log('📹 Target video details:', {
                        title: targetElement.dataset.videoTitle,
                        unlocked: targetElement.dataset.isUnlocked,
                        completed: targetElement.dataset.isCompleted
                    });
                }
            }

            // Fallback to first unlocked video
            if (!targetElement || targetElement.dataset.isUnlocked !== 'true') {
                console.log('🔄 Falling back to first unlocked video...');
                targetElement = document.querySelector('.video-item[data-is-unlocked="true"]');
                console.log('🔍 Found first unlocked video:', targetElement ? 'Yes' : 'No');
            }

            // Final fallback to any video
            if (!targetElement) {
                console.log('🔄 Final fallback to any video...');
                targetElement = document.querySelector('.video-item');
                console.log('🔍 Found any video:', targetElement ? 'Yes' : 'No');
            }

            if (targetElement) {
                console.log('✅ Loading initial video:', targetElement.dataset.videoTitle);
                this.loadVideo(targetElement, false);
            } else {
                console.error('❌ No videos found to load!');
                this.showMessage('error', 'Tidak ada video yang tersedia untuk ditonton saat ini.');
            }
        } catch (error) {
            console.error('❌ Error loading initial video:', error);
            this.showFallbackError('Gagal memuat video awal.');
        }
    }

    onVideoClick(e) {
        e.preventDefault();
        console.log('🖱️ Video click event triggered');
        
        if (this.isProcessing) {
            console.log('⏳ Already processing, ignoring click');
            this.showMessage('warning', 'Mohon tunggu, sedang memproses...');
            return;
        }

        const videoItem = e.target.closest('.video-item');
        if (!videoItem) {
            console.error('❌ Video item not found from click target');
            return;
        }

        console.log('🎬 Clicked video:', {
            title: videoItem.dataset.videoTitle,
            id: videoItem.dataset.videoId,
            unlocked: videoItem.dataset.isUnlocked,
            url: videoItem.dataset.videoUrl
        });

        if (videoItem.dataset.isUnlocked !== 'true') {
            console.log('🔒 Video is locked');
            this.showMessage('warning', 'Video ini masih terkunci. Selesaikan video sebelumnya terlebih dahulu.');
            return;
        }

        console.log('✅ Loading clicked video...');
        this.loadVideo(videoItem, true);
    }

    async loadVideo(videoItem, userInitiated = false) {
        if (this.isProcessing) {
            console.log('⏳ Already processing video load');
            return;
        }
        
        console.log('📹 Starting video load process...', {
            userInitiated,
            videoTitle: videoItem.dataset.videoTitle
        });
        
        this.isProcessing = true;
        this.showLoading(true);

        try {
            // Extract video data with validation
            const videoData = {
                id: videoItem.dataset.videoId,
                url: videoItem.dataset.videoUrl,
                title: videoItem.dataset.videoTitle,
                modulName: videoItem.dataset.modulName,
                lastTime: parseInt(videoItem.dataset.lastTime) || 0,
                hasAnswered: videoItem.dataset.hasAnswered === 'true',
                isCompleted: videoItem.dataset.isCompleted === 'true',
                isLastInModul: videoItem.dataset.isLastInModul === 'true',
                isLastModul: videoItem.dataset.isLastModul === 'true'
            };

            console.log('📊 Video data extracted:', videoData);

            // Validate video data
            if (!videoData.id || !videoData.url || !videoData.title) {
                throw new Error('Data video tidak lengkap');
            }

            // Update current video
            this.currentVideo = videoData;
            console.log('✅ Current video updated');

            // Update UI elements
            console.log('🎨 Updating UI...');
            this.updateActiveVideo(videoItem);
            
            // Update title with fallback
            const fullTitle = `${videoData.modulName} - ${videoData.title}`;
            console.log('📝 Setting video title:', fullTitle);
            this.elements.videoTitle.textContent = fullTitle;
            
            // Force title update if it didn't work
            setTimeout(() => {
                if (this.elements.videoTitle.textContent !== fullTitle) {
                    console.log('🔄 Force updating title...');
                    this.elements.videoTitle.innerHTML = fullTitle;
                }
            }, 100);

            this.hideQuestion();

            // Load video source
            console.log('📺 Loading video source:', videoData.url);
            this.elements.videoSource.src = videoData.url;
            this.elements.video.load();

            // Auto-play if user initiated
            if (userInitiated) {
                console.log('▶️ Attempting autoplay...');
                try {
                    await this.elements.video.play();
                    console.log('✅ Autoplay successful');
                } catch (error) {
                    console.log('⚠️ Autoplay blocked:', error);
                    this.showMessage('info', 'Klik tombol play untuk memulai video');
                }
            }

            console.log('✅ Video loaded successfully:', videoData.title);

        } catch (error) {
            console.error('❌ Error loading video:', error);
            this.showMessage('error', 'Gagal memuat video: ' + error.message);
        } finally {
            this.isProcessing = false;
            this.showLoading(false);
            console.log('🏁 Video load process completed');
        }
    }

    onVideoLoaded() {
        console.log('📹 Video loaded event triggered');
        
        if (!this.currentVideo) {
            console.warn('⚠️ No current video set');
            return;
        }

        // Restore last position
        if (this.currentVideo.lastTime > 0) {
            console.log(`⏰ Restoring to ${this.currentVideo.lastTime}s`);
            this.elements.video.currentTime = this.currentVideo.lastTime;
        }

        // Start tracking
        this.startWatchTimeTracking();
        console.log('✅ Video loaded handler completed');
    }

    onTimeUpdate() {
        // Throttle for performance
        if (this.debounceTimer) return;
        
        this.debounceTimer = setTimeout(() => {
            this.debounceTimer = null;
            
            const duration = this.elements.video.duration;
            const currentTime = this.elements.video.currentTime;
            
            if (duration > 0) {
                const progress = (currentTime / duration) * 100;
                // Could update individual video progress here if needed
            }
        }, 1000);
    }

    onVideoPause() {
        console.log('⏸️ Video paused');
        this.saveWatchTime(false);
    }

    async onVideoEnded() {
        console.log('🎬 Video ended');
        
        this.saveWatchTime(true);
        this.stopWatchTimeTracking();

        if (this.currentVideo.hasAnswered || this.currentVideo.isCompleted) {
            console.log('✅ Video already completed, proceeding...');
            this.handleVideoCompleted();
            return;
        }

        // Show question confirmation
        try {
            const result = await Swal.fire({
                title: '🎉 Video Selesai!',
                text: 'Siap menjawab pertanyaan untuk melanjutkan?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Ya, Siap!',
                cancelButtonText: 'Nanti Saja',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            });

            if (result.isConfirmed) {
                await this.loadQuestions();
            }
        } catch (error) {
            console.error('❌ Error showing question dialog:', error);
            // Fallback to simple confirm
            if (confirm('Video selesai. Siap menjawab pertanyaan?')) {
                this.loadQuestions();
            }
        }
    }

    async loadQuestions() {
        if (!this.currentVideo) {
            console.error('❌ No current video for loading questions');
            return;
        }

        console.log('❓ Loading questions for video:', this.currentVideo.id);
        this.showLoading(true);

        try {
            const url = this.config.routes.getQuestions.replace(':videoId', this.currentVideo.id);
            console.log('🌐 Fetching questions from:', url);
            
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('📥 Questions response:', data);

            if (!response.ok) {
                throw new Error(data.message || 'Gagal mengambil pertanyaan');
            }

                        if (data.has_answered) {
                console.log('✅ User has already answered');
                this.showMessage('info', data.message);
                this.handleVideoCompleted();
                return;
            }

            if (data.questions && data.questions.length > 0) {
                console.log('📝 Displaying question:', data.questions[0]);
                this.displayQuestion(data.questions[0]);
            } else {
                console.log('⚠️ No questions found for this video');
                this.showMessage('info', 'Video ini tidak memiliki pertanyaan.');
                this.handleVideoCompleted();
            }

        } catch (error) {
            console.error('❌ Error loading questions:', error);
            this.showMessage('error', 'Gagal memuat pertanyaan: ' + error.message);
        } finally {
            this.showLoading(false);
        }
    }

    displayQuestion(question) {
        console.log('📝 Displaying question:', question.question);
        
        try {
            // Validate question elements
            if (!this.elements.questionId || !this.elements.questionVideoId || 
                !this.elements.questionContent || !this.elements.answers) {
                throw new Error('Question form elements not found');
            }

            // Populate question form
            this.elements.questionId.value = question.id;
            this.elements.questionVideoId.value = this.currentVideo.id;
            this.elements.questionContent.innerHTML = `<h5 class="text-primary">${question.question}</h5>`;

            // Generate answer options
            let answersHtml = '';
            question.answers.forEach((answer, index) => {
                answersHtml += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="answer" 
                               id="answer_${answer.id}" value="${answer.id}" required>
                        <label class="form-check-label" for="answer_${answer.id}">
                            ${String.fromCharCode(65 + index)}. ${answer.answer}
                        </label>
                    </div>
                `;
            });

            this.elements.answers.innerHTML = answersHtml;
            this.showQuestion();

            console.log('✅ Question displayed successfully');
        } catch (error) {
            console.error('❌ Error displaying question:', error);
            this.showMessage('error', 'Gagal menampilkan pertanyaan.');
        }
    }

    async onSubmitAnswer(e) {
        e.preventDefault();
        console.log('📤 Submitting answer...');
        
        if (this.isProcessing) {
            console.log('⏳ Already processing answer submission');
            return;
        }
        
        this.isProcessing = true;
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(this.elements.questionForm);
            console.log('📋 Form data prepared');
            
            const response = await fetch(this.config.routes.submitAnswer, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('📥 Answer response:', data);

            if (!response.ok) {
                throw new Error(data.message || 'Gagal mengirim jawaban');
            }

            if (data.success) {
                console.log('✅ Correct answer!');
                await this.handleCorrectAnswer(data);
            } else {
                console.log('❌ Wrong answer');
                await this.handleWrongAnswer(data.message);
            }

        } catch (error) {
            console.error('❌ Error submitting answer:', error);
            this.showMessage('error', 'Gagal mengirim jawaban: ' + error.message);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            this.isProcessing = false;
        }
    }

    async handleCorrectAnswer(data) {
        console.log('🎉 Handling correct answer');
        
        try {
            await Swal.fire({
                title: '🎉 Selamat!',
                text: 'Jawaban Anda benar!',
                icon: 'success',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#28a745'
            });
        } catch (error) {
            // Fallback to alert
            alert('🎉 Selamat! Jawaban Anda benar!');
        }

        this.hideQuestion();
        this.markVideoCompleted();
        this.updateProgressSummary();

        if (data.modul_completed) {
            console.log('🏆 Module completed!');
            await this.handleModulCompleted();
        } else {
            console.log('➡️ Video completed, checking next...');
            await this.handleVideoCompleted();
        }
    }

    async handleWrongAnswer(message) {
        console.log('❌ Handling wrong answer');
        
        try {
            await Swal.fire({
                title: '❌ Jawaban Salah',
                text: message || 'Silakan coba lagi dengan jawaban yang berbeda.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#dc3545'
            });
        } catch (error) {
            // Fallback to alert
            alert('❌ Jawaban salah. Silakan coba lagi.');
        }

        this.elements.questionForm.reset();
    }

    async handleVideoCompleted() {
        console.log('✅ Handling video completion');
        
        const nextVideo = this.findNextVideo();
        
        if (nextVideo) {
            console.log('➡️ Next video found:', nextVideo.dataset.videoTitle);
            
            try {
                const result = await Swal.fire({
                    title: '🚀 Lanjut ke Video Berikutnya?',
                    text: `Video selanjutnya: ${nextVideo.dataset.videoTitle}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjut!',
                    cancelButtonText: 'Nanti Saja',
                    confirmButtonColor: '#007bff'
                });

                if (result.isConfirmed) {
                    this.unlockVideo(nextVideo);
                    this.loadVideo(nextVideo, true);
                }
            } catch (error) {
                // Fallback to confirm
                if (confirm(`Lanjut ke video berikutnya: ${nextVideo.dataset.videoTitle}?`)) {
                    this.unlockVideo(nextVideo);
                    this.loadVideo(nextVideo, true);
                }
            }
        } else {
            console.log('🏁 All videos completed!');
            this.showMessage('success', 'Anda telah menyelesaikan semua video yang tersedia!');
        }
    }

    async handleModulCompleted() {
        console.log('🏆 Handling module completion');
        
        try {
            const result = await Swal.fire({
                title: '🏆 Modul Selesai!',
                text: 'Selamat! Anda telah menyelesaikan modul ini.',
                icon: 'success',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Lanjut ke Modul Berikutnya',
                denyButtonText: 'Ikut Kelas Tutorial',
                cancelButtonText: 'Istirahat Dulu',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#17a2b8'
            });

            if (result.isConfirmed) {
                const nextVideo = this.findNextVideo();
                if (nextVideo) {
                    this.unlockVideo(nextVideo);
                    this.loadVideo(nextVideo, true);
                } else {
                    this.showMessage('info', 'Tidak ada modul selanjutnya yang tersedia.');
                }
            } else if (result.isDenied) {
                window.location.href = this.config.routes.kelasTutorial;
            }
        } catch (error) {
            // Fallback to confirm
            const choice = confirm('🏆 Modul selesai! Lanjut ke modul berikutnya?\n\nOK = Lanjut\nCancel = Tutorial');
            if (choice) {
                const nextVideo = this.findNextVideo();
                if (nextVideo) {
                    this.unlockVideo(nextVideo);
                    this.loadVideo(nextVideo, true);
                }
            } else {
                window.location.href = this.config.routes.kelasTutorial;
            }
        }
    }

    findNextVideo() {
        if (!this.currentVideo) {
            console.warn('⚠️ No current video to find next from');
            return null;
        }

        console.log('🔍 Finding next video after:', this.currentVideo.id);
        
        const currentVideoElement = document.querySelector(`[data-video-id="${this.currentVideo.id}"]`);
        if (!currentVideoElement) {
            console.error('❌ Current video element not found in DOM');
            return null;
        }

        // Find next video in same module
        let nextVideo = currentVideoElement.nextElementSibling;
        console.log('🔍 Next video in same module:', nextVideo ? 'Found' : 'Not found');
        
        // If not found, find first video in next module
        if (!nextVideo) {
            const currentModulSection = currentVideoElement.closest('.modul-section');
            const nextModulSection = currentModulSection ? currentModulSection.nextElementSibling : null;
            
            if (nextModulSection) {
                nextVideo = nextModulSection.querySelector('.video-item');
                console.log('🔍 First video in next module:', nextVideo ? 'Found' : 'Not found');
            }
        }

        if (nextVideo) {
            console.log('✅ Next video found:', nextVideo.dataset.videoTitle);
        } else {
            console.log('🏁 No next video found');
        }

        return nextVideo;
    }

    unlockVideo(videoElement) {
        if (!videoElement) {
            console.warn('⚠️ Cannot unlock null video element');
            return;
        }

        console.log('🔓 Unlocking video:', videoElement.dataset.videoTitle);

        videoElement.dataset.isUnlocked = 'true';
        videoElement.classList.remove('locked');
        videoElement.classList.add('unlocked');

        const link = videoElement.querySelector('.video-link');
        if (link) {
            link.classList.remove('disabled');
            link.style.pointerEvents = '';
            link.style.opacity = '';
        }

        const icon = videoElement.querySelector('.fas');
        if (icon) {
            icon.classList.remove('fa-lock', 'text-muted');
            icon.classList.add('fa-play-circle', 'text-primary');
        }

        const statusText = videoElement.querySelector('small');
        if (statusText) {
            statusText.innerHTML = '<i class="fas fa-play me-1"></i>Siap ditonton';
        }

        console.log('✅ Video unlocked successfully');
    }

    markVideoCompleted() {
        if (!this.currentVideo) {
            console.warn('⚠️ No current video to mark as completed');
            return;
        }

        console.log('✅ Marking video as completed:', this.currentVideo.title);

        const videoElement = document.querySelector(`[data-video-id="${this.currentVideo.id}"]`);
        if (!videoElement) {
            console.error('❌ Video element not found for completion marking');
            return;
        }

        videoElement.dataset.isCompleted = 'true';
        videoElement.dataset.hasAnswered = 'true';
        videoElement.classList.add('completed');

        const link = videoElement.querySelector('.video-link');
        if (link) {
            link.style.borderLeft = '4px solid #28a745';
            link.style.backgroundColor = '#f8f9fa';
        }

        const icon = videoElement.querySelector('.fas');
        if (icon) {
            icon.classList.remove('fa-play-circle', 'text-primary');
            icon.classList.add('fa-check-circle', 'text-success');
        }

        const statusText = videoElement.querySelector('small');
        if (statusText) {
            statusText.innerHTML = '<i class="fas fa-check me-1"></i>Selesai';
        }

        console.log('✅ Video marked as completed successfully');
    }

    updateActiveVideo(videoElement) {
        console.log('🎯 Updating active video state');
        
        // Remove active class from all videos
        document.querySelectorAll('.video-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to current video
        videoElement.classList.add('active');
        
        console.log('✅ Active video state updated');
    }

    updateProgressSummary() {
        console.log('📊 Updating progress summary...');
        
        try {
            const totalVideos = document.querySelectorAll('.video-item').length;
            const completedVideos = document.querySelectorAll('.video-item[data-is-completed="true"]').length;
            const percentage = totalVideos > 0 ? Math.round((completedVideos / totalVideos) * 100) : 0;

            console.log(`📈 Progress: ${completedVideos}/${totalVideos} (${percentage}%)`);

            // Update main progress with null checks
            if (this.elements.completedCount) this.elements.completedCount.textContent = completedVideos;
            if (this.elements.totalCount) this.elements.totalCount.textContent = totalVideos;
            if (this.elements.progressPercentage) this.elements.progressPercentage.style.width = percentage + '%';
            if (this.elements.progressText) this.elements.progressText.textContent = percentage + '% Selesai';
            if (this.elements.overallProgress) this.elements.overallProgress.style.width = percentage + '%';

            // Update module progress bars
            document.querySelectorAll('.modul-section').forEach(modulSection => {
                const modulVideos = modulSection.querySelectorAll('.video-item');
                const modulCompleted = modulSection.querySelectorAll('.video-item[data-is-completed="true"]');
                const modulPercentage = modulVideos.length > 0 ? Math.round((modulCompleted.length / modulVideos.length) * 100) : 0;

                const progressBar = modulSection.querySelector('.modul-progress-bar');
                const progressText = modulSection.querySelector('[id^="modulProgress-"]');
                
                if (progressBar) progressBar.style.width = modulPercentage + '%';
                if (progressText) progressText.textContent = modulPercentage + '%';
            });

            // Update achievement badges
            this.updateAchievementBadges(percentage);

            console.log('✅ Progress summary updated successfully');
        } catch (error) {
            console.error('❌ Error updating progress summary:', error);
        }
    }

        updateAchievementBadges(percentage) {
        try {
            const beginnerBadge = document.getElementById('beginnerBadge');
            const intermediateBadge = document.getElementById('intermediateBadge');
            const expertBadge = document.getElementById('expertBadge');

            // Show badges based on progress
            if (beginnerBadge && percentage >= 25) {
                beginnerBadge.style.display = 'inline-block';
            }
            if (intermediateBadge && percentage >= 50) {
                intermediateBadge.style.display = 'inline-block';
            }
            if (expertBadge && percentage >= 100) {
                expertBadge.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('❌ Error updating achievement badges:', error);
        }
    }

    startWatchTimeTracking() {
        console.log('⏱️ Starting watch time tracking...');
        this.stopWatchTimeTracking(); // Clear existing interval

        this.watchTimeInterval = setInterval(() => {
            if (!this.elements.video.paused && !this.elements.video.ended) {
                this.saveWatchTime(false);
            }
        }, 10000); // Save every 10 seconds
        
        console.log('✅ Watch time tracking started');
    }

    stopWatchTimeTracking() {
        if (this.watchTimeInterval) {
            clearInterval(this.watchTimeInterval);
            this.watchTimeInterval = null;
            console.log('⏹️ Watch time tracking stopped');
        }
    }

    async saveWatchTime(completed = false) {
        if (!this.currentVideo || this.elements.video.currentTime === 0) return;

        const currentTime = Math.floor(this.elements.video.currentTime);
        console.log(`💾 Saving watch time: ${currentTime}s (completed: ${completed})`);

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
                console.log(`✅ Watch time saved successfully: ${currentTime}s`);
                
                // Update data attribute for current video
                const activeVideo = document.querySelector('.video-item.active');
                if (activeVideo) {
                    activeVideo.dataset.lastTime = currentTime;
                }
            } else {
                console.warn('⚠️ Watch time save failed:', response.status);
            }

        } catch (error) {
            console.error('❌ Error saving watch time:', error);
        }
    }

    showQuestion() {
        console.log('❓ Showing question section');
        if (this.elements.questionSection) {
            this.elements.questionSection.style.display = 'block';
            this.elements.questionSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    hideQuestion() {
        console.log('❌ Hiding question section');
        if (this.elements.questionSection) {
            this.elements.questionSection.style.display = 'none';
        }
        if (this.elements.questionForm) {
            this.elements.questionForm.reset();
        }
    }

    showLoading(show) {
        console.log(`⏳ ${show ? 'Showing' : 'Hiding'} loading indicator`);
        
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = show ? 'block' : 'none';
        }
        
        if (show) {
            this.elements.video.classList.add('loading');
        } else {
            this.elements.video.classList.remove('loading');
        }
    }

    showMessage(type, message) {
        console.log(`📢 Showing ${type} message:`, message);
        
        // Try SweetAlert2 first
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            Swal.fire({
                icon: iconMap[type] || 'info',
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback to native alert
            alert(message);
        }
    }

    showFallbackError(message) {
        console.error('💥 Fallback error:', message);
        
        // Create simple error display
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger m-3';
        errorDiv.innerHTML = `
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
            <p>${message}</p>
            <button class="btn btn-outline-danger" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Refresh Halaman
            </button>
        `;
        
        // Insert at top of body
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }

    onVideoError(e) {
        console.error('❌ Video error occurred:', e);
        
        const error = e.target.error;
        let errorMessage = 'Terjadi kesalahan saat memuat video.';
        
        if (error) {
            switch (error.code) {
                case error.MEDIA_ERR_ABORTED:
                    errorMessage = 'Video loading dibatalkan.';
                    break;
                case error.MEDIA_ERR_NETWORK:
                    errorMessage = 'Kesalahan jaringan saat memuat video.';
                    break;
                case error.MEDIA_ERR_DECODE:
                    errorMessage = 'Video tidak dapat diputar (format error).';
                    break;
                case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                    errorMessage = 'Format video tidak didukung.';
                    break;
            }
        }
        
        console.error('Video error details:', errorMessage);
        this.showMessage('error', errorMessage + ' Silakan refresh halaman.');
    }

    onKeyPress(e) {
        // Skip if user is typing
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        switch(e.key) {
            case ' ': // Spacebar - play/pause
                e.preventDefault();
                if (this.elements.video.paused) {
                    this.elements.video.play();
                } else {
                    this.elements.video.pause();
                }
                break;
            
            case 'ArrowLeft': // Left arrow - rewind 10s
                e.preventDefault();
                this.elements.video.currentTime = Math.max(0, this.elements.video.currentTime - 10);
                break;
            
            case 'ArrowRight': // Right arrow - forward 10s
                e.preventDefault();
                this.elements.video.currentTime = Math.min(this.elements.video.duration, this.elements.video.currentTime + 10);
                break;
            
            case 'f': // F - fullscreen
            case 'F':
                e.preventDefault();
                if (this.elements.video.requestFullscreen) {
                    this.elements.video.requestFullscreen();
                }
                break;
        }
    }

    onVisibilityChange() {
        if (document.hidden) {
            // Tab not active - pause video
            if (!this.elements.video.paused) {
                this.elements.video.pause();
                this.wasPlayingBeforeHidden = true;
                console.log('⏸️ Video paused due to tab visibility change');
            }
        } else {
            // Tab active again - resume if was playing
            if (this.wasPlayingBeforeHidden) {
                this.elements.video.play();
                this.wasPlayingBeforeHidden = false;
                console.log('▶️ Video resumed due to tab visibility change');
            }
        }
    }

    // Public methods for debugging
    getCurrentVideo() {
        return this.currentVideo;
    }

    getProgress() {
        const totalVideos = document.querySelectorAll('.video-item').length;
        const completedVideos = document.querySelectorAll('.video-item[data-is-completed="true"]').length;
        return {
            completed: completedVideos,
            total: totalVideos,
            percentage: totalVideos > 0 ? Math.round((completedVideos / totalVideos) * 100) : 0
        };
    }

    // Force reload video (for debugging)
    forceReloadVideo() {
        const activeVideo = document.querySelector('.video-item.active');
        if (activeVideo) {
            console.log('🔄 Force reloading video...');
            this.loadVideo(activeVideo, false);
        }
    }

    // Debug method to check all video elements
    debugVideoElements() {
        const videos = document.querySelectorAll('.video-item');
        console.log('🔍 Debug: Found video elements:', videos.length);
        
        videos.forEach((video, index) => {
            console.log(`Video ${index + 1}:`, {
                title: video.dataset.videoTitle,
                id: video.dataset.videoId,
                url: video.dataset.videoUrl,
                unlocked: video.dataset.isUnlocked,
                completed: video.dataset.isCompleted
            });
        });
    }

    // Cleanup method
    destroy() {
        console.log('🧹 Destroying Video Learning System...');
        
        this.stopWatchTimeTracking();
        
        // Remove event listeners
        document.removeEventListener('keydown', this.onKeyPress);
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        
        console.log('✅ Video Learning System destroyed');
    }
}

// Enhanced Performance monitoring
class PerformanceMonitor {
    constructor() {
        this.startTime = performance.now();
        this.metrics = {
            videoLoads: 0,
            questionsLoaded: 0,
            answersSubmitted: 0,
            watchTimeSaves: 0,
            errors: 0
        };
        
        console.log('📈 Performance Monitor initialized');
    }

    track(event) {
        if (this.metrics.hasOwnProperty(event)) {
            this.metrics[event]++;
            console.log(`📊 Performance: ${event} (${this.metrics[event]})`);
        }
    }

    getReport() {
        const uptime = Math.round((performance.now() - this.startTime) / 1000);
        return {
            uptime: uptime + 's',
            ...this.metrics,
            memoryUsage: performance.memory ? {
                used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024) + 'MB',
                total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024) + 'MB'
            } : 'Not available'
        };
    }
}

// Enhanced initialization with better error handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Content Loaded - Starting Video Learning System...');
    
    try {
        // Check if required elements exist
        const requiredElements = ['video', 'videoSource', 'videoTitle'];
        const missingElements = requiredElements.filter(id => !document.getElementById(id));
        
        if (missingElements.length > 0) {
            console.error('❌ Missing required elements:', missingElements);
            
            // Show user-friendly error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger m-3';
            errorDiv.innerHTML = `
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                <p>Elemen video tidak ditemukan: ${missingElements.join(', ')}</p>
                <p>Silakan refresh halaman atau hubungi administrator.</p>
                <button class="btn btn-outline-danger" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Halaman
                </button>
            `;
            document.body.insertBefore(errorDiv, document.body.firstChild);
            return;
        }

        // Initialize systems
        window.videoSystem = new VideoLearningSystem();
        window.performanceMonitor = new PerformanceMonitor();
        
        // Debug methods for console
        window.debugVideo = {
            getCurrentVideo: () => window.videoSystem.getCurrentVideo(),
            getProgress: () => window.videoSystem.getProgress(),
            forceReload: () => window.videoSystem.forceReloadVideo(),
            debugElements: () => window.videoSystem.debugVideoElements(),
            getPerformance: () => window.performanceMonitor.getReport()
        };
        
        console.log('🎉 Video Learning System fully loaded!');
        console.log('🔧 Debug commands available: window.debugVideo');
        console.log('📋 Available shortcuts:');
        console.log('   Space: Play/Pause');
        console.log('   ←/→: Rewind/Forward 10s');
        console.log('   F: Fullscreen');
        
    } catch (error) {
        console.error('💥 Critical error during initialization:', error);
        
        // Show critical error to user
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Sistem Error',
                text: 'Terjadi kesalahan kritis. Silakan refresh halaman.',
                confirmButtonText: 'Refresh',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            alert('Terjadi kesalahan kritis. Halaman akan di-refresh.');
            window.location.reload();
        }
    }
});

// Enhanced global error handler
window.addEventListener('error', function(e) {
    console.error('💥 Global error:', e.error);
    
    if (window.performanceMonitor) {
        window.performanceMonitor.track('errors');
    }
    
    // Only show error dialog in debug mode
    if (window.appConfig && window.appConfig.settings.debug) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'JavaScript Error',
                text: e.error?.message || 'Unknown error occurred',
                footer: 'Check console for details',
                confirmButtonText: 'OK'
            });
        }
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    console.log('🧹 Page unloading - cleaning up...');
    
    if (window.videoSystem) {
        window.videoSystem.destroy();
    }
    
    console.log('✅ Cleanup completed');
});

// Performance report after 30 seconds
setTimeout(() => {
    if (window.performanceMonitor) {
        console.log('📊 Performance Report (30s):', window.performanceMonitor.getReport());
    }
}, 30000);

// Service Worker for
// Service Worker for caching (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('🔧 ServiceWorker registered successfully:', registration.scope);
            })
            .catch(function(error) {
                console.log('❌ ServiceWorker registration failed:', error);
            });
    });
}

// Additional utility functions for enhanced debugging
window.videoUtils = {
    // Force click on first video
    clickFirstVideo: function() {
        const firstVideo = document.querySelector('.video-item[data-is-unlocked="true"] .video-link');
        if (firstVideo) {
            console.log('🖱️ Force clicking first video...');
            firstVideo.click();
        } else {
            console.log('❌ No unlocked video found to click');
        }
    },
    
    // List all videos with their status
    listAllVideos: function() {
        const videos = document.querySelectorAll('.video-item');
        console.log('📋 All videos:');
        videos.forEach((video, index) => {
            console.log(`${index + 1}. ${video.dataset.videoTitle}`, {
                id: video.dataset.videoId,
                unlocked: video.dataset.isUnlocked,
                completed: video.dataset.isCompleted,
                url: video.dataset.videoUrl
            });
        });
    },
    
    // Check video element attributes
    checkVideoAttributes: function(videoId) {
        const video = document.querySelector(`[data-video-id="${videoId}"]`);
        if (video) {
            console.log('🔍 Video attributes:', {
                id: video.dataset.videoId,
                title: video.dataset.videoTitle,
                url: video.dataset.videoUrl,
                modulName: video.dataset.modulName,
                unlocked: video.dataset.isUnlocked,
                completed: video.dataset.isCompleted,
                hasAnswered: video.dataset.hasAnswered,
                lastTime: video.dataset.lastTime
            });
        } else {
            console.log('❌ Video not found:', videoId);
        }
    },
    
    // Force update video title
    forceUpdateTitle: function(title) {
        const titleElement = document.getElementById('videoTitle');
        if (titleElement) {
            titleElement.textContent = title;
            titleElement.innerHTML = title;
            console.log('📝 Title force updated to:', title);
        }
    },
    
    // Test video source loading
    testVideoSource: function(url) {
        const video = document.getElementById('video');
        const source = document.getElementById('videoSource');
        
        if (video && source) {
            console.log('🧪 Testing video source:', url);
            source.src = url;
            video.load();
            
            video.addEventListener('loadedmetadata', function() {
                console.log('✅ Video source loaded successfully');
            }, { once: true });
            
            video.addEventListener('error', function(e) {
                console.error('❌ Video source failed to load:', e);
            }, { once: true });
        }
    },
    
    // Check if SweetAlert is available
    checkSweetAlert: function() {
        if (typeof Swal !== 'undefined') {
            console.log('✅ SweetAlert2 is available');
            Swal.fire({
                title: 'SweetAlert Test',
                text: 'SweetAlert2 is working!',
                icon: 'success',
                timer: 2000
            });
        } else {
            console.log('❌ SweetAlert2 is not available');
            alert('SweetAlert2 is not available - using fallback alert');
        }
    },
    
    // Check app config
    checkConfig: function() {
        if (window.appConfig) {
            console.log('✅ App config available:', window.appConfig);
            console.log('🔍 Routes:', window.appConfig.routes);
            console.log('👤 User:', window.appConfig.user);
            console.log('⚙️ Settings:', window.appConfig.settings);
        } else {
            console.log('❌ App config not available');
        }
    }
};

// Mobile-specific enhancements
if (window.innerWidth <= 768) {
    console.log('📱 Mobile device detected - applying mobile optimizations');
    
    // Add mobile-specific event listeners
    document.addEventListener('touchstart', function() {
        // Enable touch interactions
    });
    
    // Optimize video for mobile
    const video = document.getElementById('video');
    if (video) {
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
    }
}

// Network status monitoring
if ('navigator' in window && 'onLine' in navigator) {
    function updateNetworkStatus() {
        const isOnline = navigator.onLine;
        console.log(`🌐 Network status: ${isOnline ? 'Online' : 'Offline'}`);
        
        if (!isOnline) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Koneksi Terputus',
                    text: 'Periksa koneksi internet Anda',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
    }
    
    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
}

// Auto-save user preferences to localStorage
const UserPreferences = {
    save: function(key, value) {
        if (typeof Storage !== 'undefined') {
            localStorage.setItem(`videoLearning_${key}`, JSON.stringify(value));
        }
    },
    
    load: function(key, defaultValue = null) {
        if (typeof Storage !== 'undefined') {
            const stored = localStorage.getItem(`videoLearning_${key}`);
            return stored ? JSON.parse(stored) : defaultValue;
        }
        return defaultValue;
    },
    
    // Save video progress locally as backup
    saveVideoProgress: function(videoId, time) {
        this.save('lastVideo', { id: videoId, time: time, timestamp: Date.now() });
    },
    
    // Load last video progress
    loadVideoProgress: function() {
        return this.load('lastVideo');
    }
};

// Keyboard shortcuts help
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === '?') {
        e.preventDefault();
        
        const helpContent = `
            <div class="text-start">
                <h6>🎮 Video Controls:</h6>
                <p><kbd>Space</kbd> - Play/Pause video</p>
                <p><kbd>←</kbd> - Mundur 10 detik</p>
                <p><kbd>→</kbd> - Maju 10 detik</p>
                <p><kbd>F</kbd> - Fullscreen</p>
                
                <h6 class="mt-3">🔧 Debug Commands (Console):</h6>
                <p><code>window.debugVideo.getCurrentVideo()</code> - Info video saat ini</p>
                <p><code>window.debugVideo.getProgress()</code> - Progress belajar</p>
                <p><code>window.debugVideo.forceReload()</code> - Reload video paksa</p>
                <p><code>window.debugVideo.debugElements()</code> - Debug semua video</p>
                <p><code>window.videoUtils.listAllVideos()</code> - List semua video</p>
                
                <h6 class="mt-3">🆘 Troubleshooting:</h6>
                <p><code>window.videoUtils.clickFirstVideo()</code> - Klik video pertama</p>
                <p><code>window.videoUtils.checkConfig()</code> - Cek konfigurasi</p>
                <p><code>window.videoUtils.checkSweetAlert()</code> - Test SweetAlert</p>
            </div>
        `;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '⌨️ Keyboard Shortcuts & Debug Help',
                html: helpContent,
                width: '600px',
                confirmButtonText: 'OK'
            });
        } else {
            console.log('📋 Keyboard Shortcuts & Debug Help:');
            console.log('Space: Play/Pause, ←/→: Seek, F: Fullscreen');
            console.log('Ctrl+Shift+?: Show this help');
            console.log('Debug commands available in window.debugVideo and window.videoUtils');
        }
    }
});

// Final initialization check
setTimeout(() => {
    console.log('🔍 Final system check...');
    
    // Check if video system is properly initialized
    if (window.videoSystem) {
        console.log('✅ Video system initialized');
        
        // Check if video is loaded
        const video = document.getElementById('video');
        const videoSource = document.getElementById('videoSource');
        
        if (video && videoSource && videoSource.src) {
            console.log('✅ Video source is set:', videoSource.src);
        } else {
            console.warn('⚠️ Video source not set - trying to load first video...');
            if (window.videoUtils) {
                window.videoUtils.clickFirstVideo();
            }
        }
        
        // Check active video
        const activeVideo = document.querySelector('.video-item.active');
        if (activeVideo) {
            console.log('✅ Active video found:', activeVideo.dataset.videoTitle);
        } else {
            console.warn('⚠️ No active video found');
        }
        
    } else {
        console.error('❌ Video system not initialized!');
    }
    
    console.log('🎉 System check completed');
}, 2000);

// Export for global access
window.VideoLearningSystem = VideoLearningSystem;
window.PerformanceMonitor = PerformanceMonitor;
window.UserPreferences = UserPreferences;

console.log('📦 Video Learning System module loaded completely');
