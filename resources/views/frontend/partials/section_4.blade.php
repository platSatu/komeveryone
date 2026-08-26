<!-- SECTION 4: KURIKULUM - FULLSCREEN BG -->
<section id="section4" class="section-pad" style="background: url('{{ asset('frontend/BG_01.png') }}') center center no-repeat; background-size: cover; min-height: 100vh;">
    <div class="container-fluid px-0 mx-0">
        <div class="text-center mb-5">
            <h2 class="font-heading fw-bold section-title" style="font-family: 'Space Grotesk', sans-serif !important; color: #000000;">Kurikulum</h2>
        </div>
        
        <!-- Playlist - FULLWIDTH -->
        <div class="playlist-container">
            
            <!-- Item 1 -->
            <div class="playlist-item" role="button" tabindex="0">
                <span class="playlist-number">110</span>
                <div class="playlist-content">
                    <h5 class="font-heading fw-bold mb-0">110 - Dasar-Dasar Kekristenan</h5>
                    <p class="font-body text-light small mb-0 d-none d-md-block">Hidup Manusia · Kejatuhan Manusia · Keselamatan · Berkat Keselamatan · Baptisan Air</p>
                </div>
            </div>
            
            <!-- Item 2 -->
            <div class="playlist-item" role="button" tabindex="0">
                <span class="playlist-number">120</span>
                <div class="playlist-content">
                    <h5 class="font-heading fw-bold mb-0">120 - Kekristenan yang Bertumbuh</h5>
                    <p class="font-body text-light small mb-0 d-none d-md-block">Doa, Pujian & Penyembahan · Saat Teduh · Mendengar Suara Tuhan · Tertanam dalam Gereja Lokal</p>
                </div>
            </div>
            
            <!-- Item 3 -->
            <div class="playlist-item" role="button" tabindex="0">
                <span class="playlist-number">130</span>
                <div class="playlist-content">
                    <h5 class="font-heading fw-bold mb-0">130 - Mengenal Allah</h5>
                    <p class="font-body text-light small mb-0 d-none d-md-block">Mengenal Allah yang Benar · Firman yang Menjadi Manusia · Kuasa Salib Kristus · Pribadi Roh Kudus</p>
                </div>
            </div>
            
            <!-- Item 4 -->
            <div class="playlist-item" role="button" tabindex="0">
                <span class="playlist-number">140</span>
                <div class="playlist-content">
                    <h5 class="font-heading fw-bold mb-0">140 - Kehidupan yang Memberi Dampak</h5>
                    <p class="font-body text-light small mb-0 d-none d-md-block">Kehidupan yang Menjadi Berkat · Bersaksi bagi Kristus · Menemukan Panggilan Pelayanan · Sembilan Karunia Roh Kudus</p>
                </div>
            </div>
            
        </div>
        
        <!-- Button CTA -->
        <div class="text-center mt-4 pb-4">
            <p class="font-body mb-3 text-white" style="font-size: 1.5rem; font-family: 'Space Grotesk', sans-serif !important;">Siap memulai perjalanan imanmu?</p>
            <a href="https://komeveryone.gbigatsu.id/app/register" class="btn btn-primary btn-lg font-body fw-medium px-5 py-3" style="background: #EC5310;">
                Daftar Sekarang!
            </a>
        </div>
        
    </div>
</section>

<style>
/* --- SECTION 4: KURIKULUM - FULLSCREEN BG --- */

/* Section - Fullscreen */
.section-pad {
    padding-top: 80px !important;
    padding-bottom: 80px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    min-height: 100vh; /* FULLSCREEN */
    display: flex;
    align-items: center;
}

/* Background Image */
#section4 {
    background: url('{{ asset('frontend/BG_01.png') }}') center center no-repeat !important;
    background-size: cover !important;
}

/* Container */
.container-fluid.px-0.mx-0 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Playlist Container */
.playlist-container {
    width: 100%;
    max-width: 100%;
}

/* Playlist Item */
.playlist-item {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.95); /* Putih transparan */
    border-bottom: 1px solid #e9ecef;
    padding: 50px 60px;
    margin: 0;
    width: 100%;
    max-width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}

.playlist-item:first-child {
    border-top: 1px solid #e9ecef;
}

.playlist-item:focus {
    outline: none;
}

/* HOVER: #78C7B2 + MAJU */
.playlist-item:hover {
    background: #78C7B2 !important;
    transform: translateX(15px);
}

/* Saat Hover - Teks Putih */
.playlist-item:hover .playlist-number,
.playlist-item:hover h5,
.playlist-item:hover p {
    color: #ffffff !important;
}

/* Active */
.playlist-item:active {
    transform: translateX(5px);
}

/* Number */
.playlist-number {
    font-size: 1.8rem;
    font-weight: 800;
    color: #333;
    min-width: 60px;
    transition: all 0.3s ease;
}

/* Content */
.playlist-content {
    flex: 1;
}

/* Title */
.playlist-item h5 {
    font-size: 1.2rem;
    color: #333;
    transition: all 0.3s ease;
}

/* Description */
.playlist-item p {
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

/* Section Title */
/* .section-title {
    color: #ffffff;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
} */

/* Responsive */
@media (max-width: 768px) {
    .playlist-item {
        padding: 40px 30px;
    }
    
    .playlist-number {
        font-size: 1.4rem;
        min-width: 45px;
    }
    
    .playlist-item h5 {
        font-size: 1rem;
    }
    
    .playlist-item p {
        display: none;
    }
    
    .playlist-item:hover {
        transform: translateX(8px);
    }
}

@media (max-width: 576px) {
    .playlist-item {
        padding: 30px 20px;
    }
    
    .playlist-number {
        font-size: 1.2rem;
        min-width: 35px;
    }
    
    .playlist-item h5 {
        font-size: 0.9rem;
    }
    
    .section-title {
        font-size: 1.8rem !important;
    }
}
</style>