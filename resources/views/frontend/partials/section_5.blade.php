<!-- SECTION 5: PENGAJAR (SLIDER) -->
<section id="section5" class="section-pad bg-section-alt">
    <div class="container-fluid px-4">
        <div class="text-center mb-5">
            <h2 class="font-heading fw-bold section-title">Pengajar</h2>
            <p class="font-body text-secondary mt-3">Belajar dari mereka yang hidup dan melayani dalam kebenaran yang mereka ajarkan</p>
            <p class="font-body text-secondary">Lebih dari 800 guru berpengalaman dari seluruh Indonesia dipilih, dilatih, dan berdedikasi</p>
        </div>
        
        <!-- Controls Di Atas (Prev - Bullets - Next) -->
        <div class="slider-controls-top">
            <button class="slider-btn" type="button" data-bs-target="#pengajarSlider" data-bs-slide="prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
            </button>
            
            <!-- <div class="carousel-indicators pengajar-indicators">
                <button type="button" data-bs-target="#pengajarSlider" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#pengajarSlider" data-bs-slide-to="1"></button>
            </div> -->
            
            <button class="slider-btn" type="button" data-bs-target="#pengajarSlider" data-bs-slide="next">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
            </button>
        </div>
        
        <!-- Slider Carousel -->
        <div id="pengajarSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            
            <!-- Slides -->
            <div class="carousel-inner">
                
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="row g-4 justify-content-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/12.jpg') }}" alt="Pdm. Rudi" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdm. Rudi Julianto Limuria, MA</span>
                                    <p class="font-body small text-secondary mb-0">Pengajar KOM, spesialis apologetika & keuangan, memperlengkapi jemaat dengan ajaran yang kontekstual.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/14.jpg') }}" alt="Pdm. Robbyanto" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdm. Robbyanto Tenggala</span>
                                    <p class="font-body small text-secondary mb-0">Perintis Sub Divisi KOM sejak 2004, konsisten membangun fondasi pengajaran yang memperlengkapi jemaat. </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/t_7.jpg') }}" alt="Pdt. Rudi Darmawan, M.A" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdt. Rudi Darmawan, M.A</span>
                                    <p class="font-body small text-secondary mb-0">Pengajar KOM 100 & 300, membina dasar iman dan memimpin jemaat menuju kedewasaan rohani.  </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/13.jpg') }}" alt="Pdm. Budi Muljono, M.Th " class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdm. Budi Muljono, M.Th </span>
                                    <p class="font-body small text-secondary mb-0">Kepala Subdep Jemaat, membina ratusan keluarga & memperkuat dasar iman lewat pengajaran KOM. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="row g-4 justify-content-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/t_4.jpg') }}" alt="Pdt. Dr. Dony Lubianto" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdt. Dr. Dony Lubianto</span>
                                    <p class="font-body small text-secondary mb-0">Gembala GBI Poris & pengajar KOM, dosen STT yang giat membentuk jemaat lewat pengajaran firman. </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/15.jpg') }}" alt="Pdm. Dr. Dio Angga Pradipta, M.Th" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdm. Dr. Dio Angga Pradipta, M.Th</span>
                                    <p class="font-body small text-secondary mb-0">Wakil Gembala, Mentor rohani & pengajar KOM. Spesialis Teologi Pentakosta, Sistematika, & Doktrin Alkitab. </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/Samuel-Kundimang.jpg') }}" alt="Pdt. Samuel Kundimang S.Th, MACM" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdt. Samuel Kundimang S.Th, MACM</span>
                                    <p class="font-body small text-secondary mb-0">Dalam konteks pengembangan dan pengajaran kurikulum KOM, beliau memiliki spesialisasi pada Materi Biblika dan Doktrinal.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/Robertus-Purwadi-(CK7).jpg') }}" alt="Pdm. Robertus Purwadi" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdt. Robertus Purwadi SPd, SE</span>
                                    <p class="font-body small text-secondary mb-0">seorang hamba Tuhan yang melayani sebagai Wakil Gembala, Pengkhotbah, dan Pengajar di GBI PRJ (CK7)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="carousel-item">
                    <div class="row g-4 justify-content-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/s3_4.jpg') }}" alt="Pdp. Roganda Marbun, S.Th. " class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdp. Roganda Marbun, S.Th. </span>
                                    <p class="font-body small text-secondary mb-0">Pengkhotbah, Pengajar KOM dengan spesialisasi Pendalaman Alkitab melalui kajian yang sistematis terhadap PL dan PB.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/s3_1.jpg') }}" alt="Pdm. Gracia Tirtawirya, S.Sn., M.Th." class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdm. Gracia Tirtawirya, S.Sn., M.Th.</span>
                                    <p class="font-body small text-secondary mb-0">Memiliki passion dalam teaching dan counseling, serta Pengajar KOM spesialis Modul Praktikal.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/s3_2.jpeg') }}" alt="Pdp. Maria Magdalena, M.Th." class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdp. Maria Magdalena, M.Th.</span>
                                    <p class="font-body small text-secondary mb-0">Pengajar KOM dengan spesialisasi Modul Praktikal, serta memiliki  passion dalam pelayanan misi ke daerah pedalaman sebagai kesaksian hidup melalui Firman Tuhan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="{{ asset('frontend/s3_3.jpg') }}" alt="Pdt. Dr. Hendrik Timadius, MBA" class="w-100">
                                </div>
                                <div class="card-body text-center p-4">
                                    <span class="badge rounded-pill mb-2" style="background:#FFD27F; color: #000;">Pdt. Dr. Hendrik Timadius, MBA</span>
                                    <p class="font-body small text-secondary mb-0">Ka. Tim Teologi, Gembala, Pengajar KOM, spesialis Second Coming, Salvation dan  Mengenal Allah yang Benar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<style>
/* --- SECTION 5: PENGAJAR - PERBAIKAN POSISI FOTO --- */

/* Card Image Wrapper -untuk positioning yang lebih baik */
.card-img-wrapper {
    width: 100%;
    height: 280px;
    overflow: hidden;
    position: relative;
    background-color: #f0f0f0;
}

.card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 20%; /* Geser foto ke bawah sedikit agar wajah terlihat */
    transition: transform 0.4s ease;
}

/* Zoom effect saat hover */
.card:hover .card-img-wrapper img {
    transform: scale(1.05);
    object-position: center 25%;
}

/* Card styling */
#section5 .card {
    transition: all 0.3s ease;
    background: #fff;
}

#section5 .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

/* Badge styling */
#section5 .badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 8px 16px;
}

/* Controls - Sama seperti sebelumnya */
.slider-controls-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.slider-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #ddd;
    background-color: #fff;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.slider-btn:hover {
    background-color: #70A1E3;
    color: #fff;
    border-color: #70A1E3;
}

.slider-btn:focus {
    outline: none;
    box-shadow: none;
}

.pengajar-indicators {
    display: flex;
    gap: 8px;
}

.pengajar-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #dee2e6;
    border: none;
    padding: 0;
    transition: all 0.3s ease;
}

.pengajar-indicators button.active {
    background-color: #70A1E3;
    transform: scale(1.3);
}

.carousel-control-prev,
.carousel-control-next {
    display: none;
}

/* Responsive */
@media (max-width: 768px) {
    .slider-controls-top {
        margin-bottom: 20px;
        gap: 10px;
    }
    
    .slider-btn {
        width: 35px;
        height: 35px;
    }
    
    .card-img-wrapper {
        height: 240px;
    }
    
    .card-img-wrapper img {
        object-position: center 15%; /* Sedikit adjust untuk mobile */
    }
}
</style>