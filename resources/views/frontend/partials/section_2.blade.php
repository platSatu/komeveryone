
<section id="section2" class="section-about">
    <div class="container">
        <div class="row align-items-center g-5">
            
            <div class="col-md-6" style="background-color: #FFD27F; border-top-left-radius: 50px; padding:90px;">
                <h1 class="font-heading fw-bold section-title">Tentang Kelas KOM Everyone Digital</h1>
            </div>
            
            
            <div class="col-md-6">
                <p class="font-body text-secondary lh-lg description">
                    Sejak 1995, KOM telah menjadi fondasi pemuridan dan telah mengajar lebih dari <strong>110.000 alumni</strong>. 
                    Kini, dalam era digital, KOM bertransformasi menjadi <strong>KOM EveryOne Digital</strong> yang hadir untuk menjangkau 
                    lebih luas tanpa batas lokasi dan jadwal. Materi yang sama, kualitas yang terstandar, kini bisa diakses oleh siapa saja.
                </p>
            </div>
        </div>
    </div>
</section>

<style>
/* --- SECTION 2: ABOUT - LEBIH TINGGI --- */

/* Section - LEBIH TINGGI */
.section-about {
    padding: 100px 0; /* Lebih tinggi dari sebelumnya */
    background-color: #fff;
    position: relative;
}

/* Section Title */
.section-title {
    font-size: 3rem;
    font-weight: 800;
    color: #1a1a1a;
    position: relative;
    display: inline-block;
}

/* Garis bawah title */
.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(135deg, #70A1E3, #78C7B2);
}

/* Description */
.description {
    font-size: 1.3rem;
    line-height: 1.9;
    
}

.description strong {
    color: #FFD27F;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .section-about {
        padding: 60px 0; /* Lebih tinggi juga di mobile */
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .description {
        font-size: 1rem;
    }
}
</style>