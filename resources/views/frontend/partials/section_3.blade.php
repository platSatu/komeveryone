<!-- SECTION 3: WHY KOM -->
<section id="section3" class="section-pad bg-section-alt">
    <div class="container-fluid px-0">
        <div class="text-center mb-4">
            <h2 class="font-heading fw-bold section-title">Jadi kenapa harus KOM Everyone Digital ?</h2>
        </div>
        
        <!-- 2 Baris x 2 Kolom -->
        <div class="row g-0 text-center justify-content-center">
            
            <!-- Card 1 - Jam -->
            <div class="col-md-6 col-6">
                <div class="card h-100 border-0">
                    <div class="card-body">
                        <h1 class="font-heading fw-semibold mb-1" style="font-size: 2.5rem;">Atur Jadwalmu Sendiri!</h1>
                    </div>
                    
                    <!-- Container Jam -->
                    <div class="jam-container">
                        <!-- Background Jam -->
                        <img src="{{ asset('frontend/jam.svg') }}" alt="Jam" class="jam-bg">
                        
                        <!-- Jarum Jam (Berputar) -->
                        <img src="{{ asset('frontend/jarum_2.svg') }}" alt="Jarum" class="jam-jarum">
                    </div>
                    
                    <div class="card-body">
                        <p class="font-body small mb-0" style="font-size: 1rem;">Akses seluruh materi secara online dan belajar sesuai ritme yang paling nyaman untukmu</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 - Kurikulum -->
            <div class="col-md-6 col-6">
                <div class="card h-100 border-0">
                    <div class="card-body">
                        <h1 class="font-heading fw-semibold mb-1" style="font-size: 2.5rem;">Kurikulum yang telah teruji</h1>
                    </div>
                    <div class="jam-container">
                        <img src="{{ asset('frontend/kurikulum.png') }}" alt="Kurikulum" class="jam-bg">
                        <img src="{{ asset('frontend/toga.png') }}" alt="Toga" class="toga-animasi">
                    </div>
                    <div class="card-body">
                        <p class="font-body small mb-0" style="font-size: 1rem;">Materi pembelajaran yang telah digunakan oleh lebih dari 110.000 alumni</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 3 - Sesi -->
            <div class="col-md-6 col-6">
                <div class="card h-100 border-0">
                    <div class="card-body">
                        <h1 class="font-heading fw-semibold mb-1" style="font-size: 2.5rem;">Hanya 4 sesi tutorial</h1>
                    </div>
                    <div class="jam-container">
                        <img src="{{ asset('frontend/tutor.png') }}" alt="Tutor" class="jam-bg">
                        <img src="{{ asset('frontend/play.png') }}" alt="Play" class="tutor-play">
                        <img src="{{ asset('frontend/pause.png') }}" alt="Pause" class="tutor-pause">
                    </div>
                    <div class="card-body">
                        <p class="font-body small mb-0" style="font-size: 1rem;">Ikuti 4 sesi tutorial online sebagai pendamping proses belajarmu</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 4 - Diskusi -->
            <div class="col-md-6 col-6">
                <div class="card h-100 border-0">
                    <div class="card-body">
                        <h1 class="font-heading fw-semibold mb-1" style="font-size: 2.5rem;">Berdiskusi lebih leluasa</h1>
                    </div>
                    <div class="jam-container">
                        <img src="{{ asset('frontend/diskusi.png') }}" alt="Diskusi" class="jam-bg">
                        <img src="{{ asset('frontend/chat-2.png') }}" alt="Diskusi" class="jam-bg">
                        <img src="{{ asset('frontend/chat-1.png') }}" alt="Diskusi" class="jam-bg">
                        <img src="{{ asset('frontend/chat-3.png') }}" alt="Diskusi" class="jam-bg">
                    </div>
                    <div class="card-body">
                        <p class="font-body small mb-0" style="font-size: 1rem;">Nikmati ruang diskusi online yang lebih terbuka</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <style>
    @media (max-width: 991px) {
        h1[style*="2.5rem"] {
            font-size: 2rem !important;
        }
    }
    
    @media (max-width: 768px) {
        h1[style*="2.5rem"] {
            font-size: 1.5rem !important;
        }
    }
    
    @media (max-width: 576px) {
        h1[style*="2.5rem"] {
            font-size: 1.25rem !important;
        }
    }
</style>
</section>