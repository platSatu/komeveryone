<!-- SECTION 1: HERO / HEADER -->
<section id="section1" class="section-hero d-flex align-items-center justify-content-center" style="min-height: 130vh; padding-top: 0px; background: #f2ece3;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center">
                
<div style="position: relative; width: 320px; height: 200px; margin: 0 auto; display: block;">
  
  
  <img src="{{ asset('frontend/bg_logo.svg') }}" alt="Logo" 
       style="width: 100%; height: auto; position: absolute; top: 50%; left: 50%; 
              transform: translate(-50%, -50%); z-index: 1;">
  
  <!-- API - di depan logo tapi tidak blocking -->
  <svg id="api-unggui" width="100" height="120" viewBox="0 0 135.11 273.78" 
       style="position: absolute; top: 50%; left: 50%; 
              transform: translate(-50%, -50%); z-index: 2;">
    <defs>
      <style>
        .cls-1 { fill: none; }
        .cls-2 { fill: url(#linear-gradient); }
        .cls-3 { clip-path: url(#clippath); }
      </style>
      
      <clipPath id="clippath">
        <path class="cls-1" d="M50.1,53.05c-11.25,22.2-2.27,36.37,2.53,44.8,4.79,8.43,7.69,8.65,10.36,16.45,2.22,6.42,1.41,14.01.28,16.54-1.12,2.54-2.8,1.71-2.8,1.71,0,0-19.11-19.67-25.3-34.29-6.19-14.62-6.76-19.12-6.76-19.12,0,0-19.1,34.3-19.1,58.49,0,19.68,10.12,37.66,26.97,56.22,16.88,18.56,19.13,29.25,18.56,42.75-.57,13.5-5.06,23.05-5.06,23.05,0,0,18.55-6.76,33.19-47.23,0,0,6.19-20.81,1.96-37.67-4.21-16.86-15.17-28.68-15.17-30.36s8.15-4.51,12.93-1.13c4.77,3.37,14.35,13.49,14.35,28.41s-3.66,34-3.66,34c0,0,23.05-24.45,28.39-47.5,5.34-23.06,2.19-33.73-4.22-42.18-8.41-11.05-28.1-3.37-28.1-3.37,0,0,6.18-22.21,3.07-32.33-3.07-10.13-17.23-21.65-19.11-34.59-1.85-12.66,1.39-24.45,1.39-24.45,0,0-13.46,9.59-24.7,31.82"/>
      </clipPath>
      
      <linearGradient id="linear-gradient" x1="-136.77" y1="623.43" x2="-126.07" y2="623.43" gradientTransform="translate(-13817.1331 3067.2785) rotate(90) scale(22.2705 -22.2705)" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#ffc615"/>
        <stop offset=".19" stop-color="#fbb419"/>
        <stop offset=".61" stop-color="#f69321"/>
        <stop offset=".86" stop-color="#f27027"/>
        <stop offset="1" stop-color="#f15b2b"/>
      </linearGradient>
    </defs>
    
    <g class="cls-3">
      <path class="cls-2" d="M50.1,53.05c-11.25,22.2-2.27,36.37,2.53,44.8,4.79,8.43,7.69,8.65,10.36,16.45,2.22,6.42,1.41,14.01.28,16.54-1.12,2.54-2.8,1.71-2.8,1.71,0,0-19.11-19.67-25.3-34.29-6.19-14.62-6.76-19.12-6.76-19.12,0,0-19.1,34.3-19.1,58.49,0,19.68,10.12,37.66,26.97,56.22,16.88,18.56,19.13,29.25,18.56,42.75-.57,13.5-5.06,23.05-5.06,23.05,0,0,18.55-6.76,33.19-47.23,0,0,6.19-20.81,1.96-37.67-4.21-16.86-15.17-28.68-15.17-30.36s8.15-4.51,12.93-1.13c4.77,3.37,14.35,13.49,14.35,28.41s-3.66,34-3.66,34c0,0,23.05-24.45,28.39-47.5,5.34-23.06,2.19-33.73-4.22-42.18-8.41-11.05-28.1-3.37-28.1-3.37,0,0,6.18-22.21,3.07-32.33-3.07-10.13-17.23-21.65-19.11-34.59-1.85-12.66,1.39-24.45,1.39-24.45,0,0-13.46,9.59-24.7,31.82">
        <animate attributeName="d" 
                values="M50.1,53.05c-11.25,22.2-2.27,36.37,2.53,44.8,4.79,8.43,7.69,8.65,10.36,16.45,2.22,6.42,1.41,14.01.28,16.54-1.12,2.54-2.8,1.71-2.8,1.71,0,0-19.11-19.67-25.3-34.29-6.19-14.62-6.76-19.12-6.76-19.12,0,0-19.1,34.3-19.1,58.49,0,19.68,10.12,37.66,26.97,56.22,16.88,18.56,19.13,29.25,18.56,42.75-.57,13.5-5.06,23.05-5.06,23.05,0,0,18.55-6.76,33.19-47.23,0,0,6.19-20.81,1.96-37.67-4.21-16.86-15.17-28.68-15.17-30.36s8.15-4.51,12.93-1.13c4.77,3.37,14.35,13.49,14.35,28.41s-3.66,34-3.66,34c0,0,23.05-24.45,28.39-47.5,5.34-23.06,2.19-33.73-4.22-42.18-8.41-11.05-28.1-3.37-28.1-3.37,0,0,6.18-22.21,3.07-32.33-3.07-10.13-17.23-21.65-19.11-34.59-1.85-12.66,1.39-24.45,1.39-24.45,0,0-13.46,9.59-24.7,31.82;
                M50.1,48c-13,24-3,38,1,46,4,8,6,9,9,17,2,6,2,14,1,16-1,3-3,2-3,2,0,0-21-21-27-35-6-15-8-20-8-20,0,0-20,35-19,59,0,20,9,38,26,57,17,19,18,30,18,43-.6,14-6,24-6,24,0,0,19-8,34-48,0,0,7-21,3-38-4-17-16-29-16-31s8-5,13-2c5,3,15,14,15,28s-4,34-4,34c0,0,24-25,29-48,5-23,3-34-3-43-9-11-29-4-29-4,0,0,7-23,4-33-3-10-18-22-20-35-2-13,2-25,2-25,0,0-14,10-26,33;
                M50.1,50c-10,23-1,35,3,45,5,8,8,9,11,17,2,6,1,14,0,16-1,3-3,2-3,2,0,0-20-20-26-35-6-14-7-19-7-19,0,0-19,35-19,59,0,20,10,38,27,57,17,19,20,30,19,43-.6,14-5,24-5,24,0,0,19-7,33-48,0,0,6-21,2-38-4-17-15-29-15-31s8-5,13-2c4,3,14,13,14,28s-4,34-4,34c0,0,23-24,28-47,5-23,2-33-4-42-8-11-28-4-28-4,0,0,6-22,3-32-3-10-17-22-19-35-2-13,1-24,1-24,0,0-13,10-25,32;
                M50.1,53.05c-11.25,22.2-2.27,36.37,2.53,44.8,4.79,8.43,7.69,8.65,10.36,16.45,2.22,6.42,1.41,14.01.28,16.54-1.12,2.54-2.8,1.71-2.8,1.71,0,0-19.11-19.67-25.3-34.29-6.19-14.62-6.76-19.12-6.76-19.12,0,0-19.1,34.3-19.1,58.49,0,19.68,10.12,37.66,26.97,56.22,16.88,18.56,19.13,29.25,18.56,42.75-.57,13.5-5.06,23.05-5.06,23.05,0,0,18.55-6.76,33.19-47.23,0,0,6.19-20.81,1.96-37.67-4.21-16.86-15.17-28.68-15.17-30.36s8.15-4.51,12.93-1.13c4.77,3.37,14.35,13.49,14.35,28.41s-3.66,34-3.66,34c0,0,23.05-24.45,28.39-47.5,5.34-23.06,2.19-33.73-4.22-42.18-8.41-11.05-28.1-3.37-28.1-3.37,0,0,6.18-22.21,3.07-32.33-3.07-10.13-17.23-21.65-19.11-34.59-1.85-12.66,1.39-24.45,1.39-24.45,0,0-13.46,9.59-24.7,31.82" 
                dur="0.6s" 
                repeatCount="indefinite"/>
      </path>
    </g>
    
    <!-- Api kecil di atas -->
    <path fill="#FFD700" d="M60,30 Q50,10 60,-5 Q70,10 60,30">
      <animate attributeName="d" 
              values="M60,30 Q50,10 60,-5 Q70,10 60,30;M60,30 Q48,5 60,-10 Q72,5 60,30;M60,30 Q52,8 60,-8 Q68,8 60,30;M60,30 Q50,10 60,-5 Q70,10 60,30" 
              dur="0.4s" repeatCount="indefinite"/>
    </path>
  </svg>
</div>

<style>
#api-unggui { display: block; }
</style>
                
                <!-- Description with Typing Animation -->
                <h1 class="lead font-body mb-5" style="font-family: 'Space Grotesk', sans-serif !important; font-size: 2.5rem; font-weight: 600; margin-top:70px;">
                    <span class="typewriter"></span>
                </h1>
                
                <!-- CTA Button -->
                <a href="{{ route('kom.register') }}" class="btn btn-primary btn-lg font-body fw-medium px-5 py-3 rounded-pill" style="font-family: 'Space Grotesk', sans-serif !important; font-size: 1.2rem; background: #EC5310;">DAFTAR SEKARANG !</a>
            </div>
        </div>
    </div>
    <!-- Icon Goyang -->


</section>

<!-- JavaScript for Typing Animation -->
<script>
    const text = "Fondasi iman yang kuat dan sudah terbukti lebih dari 20 tahun, kini hadir dalam genggamanmu";
    const speed = 100; // Kecepatan ketik (ms)
    let index = 0;

    function typeWriter() {
        if (index < text.length) {
            document.querySelector(".typewriter").innerHTML += text.charAt(index);
            index++;
            setTimeout(typeWriter, speed);
        }
    }

    // Jalankan animasi saat halaman load
    window.onload = typeWriter;
</script>

<!-- CSS for Cursor Effect -->
<style>
    .typewriter::after {
        content: '|';
        animation: blink 1s infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
</style>

<!-- SECTION 2: STATS (DI BAWAH HERO) -->
<!-- SECTION 2: STATS - FULLSCREEN 3 KOLOM -->
<section id="section2" class="section-stats">
    <div class="container-fluid h-100">
        <div class="row h-100 g-0">
            
            <!-- Stat 1: Alumni -->
            <div class="col-4 stat-col">
                <div class="stat-item">
                    <h2 class="stat-number">
                        <span class="stat-value" data-target="100">0</span><span class="stat-suffix">K+</span>
                    </h2>
                    <p class="stat-label">Alumni</p>
                </div>
            </div>
            
            <!-- Stat 2: Modul -->
            <div class="col-4 stat-col">
                <div class="stat-item">
                    <h2 class="stat-number">
                        <span class="stat-value" data-target="27">0</span>
                    </h2>
                    <p class="stat-label">Modul</p>
                </div>
            </div>
            
            <!-- Stat 3: Sesi -->
            <div class="col-4 stat-col">
                <div class="stat-item">
                    <h2 class="stat-number">
                        <span class="stat-value" data-target="4">0</span>
                    </h2>
                    <p class="stat-label">Sesi</p>
                </div>
            </div>
            
        </div>
    </div>
</section>

<style>




/* Section */
.section-stats {
    min-height: auto;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #fafbfc 0%, #f0f4f8 100%);
    padding: 20px 0;       
}

/* Kolom */
.stat-col {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px 10px;     
}

/* Stat Item - Wadah */
.stat-item {
    text-align: center;
    padding: 10px;         
}

/* ANGKA - Jarak bawah angka ke label */
.stat-number {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 900;
    font-size: 3rem;       
    line-height: 1;
    margin-bottom: 2px;    
    background: #70A1E3;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* K+ */
.stat-suffix {
    font-size: 1.2rem;      /* ← KECILKAN: dari 3rem */
    background: none;
    -webkit-text-fill-color: #70A1E3;
}

/* Label - Jarak ke bawah */
.stat-label {
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    font-size: 0.85rem;     /* ← KECILKAN: dari 1.5rem */
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
}

/* Garis bawah - Hapus atau lebih kecil */
.stat-label::after {
    content: '';
    display: block;
    width: 40px;             /* ← KECILKAN: dari 60px */
    height: 3px;
    background: #70A1E3;
    margin: 8px auto 0;      /* ← KECILKAN: dari 16px */
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
    .stat-number {
        font-size: 2.5rem;
    }
    .stat-suffix {
        font-size: 1rem;
    }
    .stat-label {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .section-stats {
        padding: 15px 0;
    }
    .stat-col {
        padding: 10px 15px;
    }
    .stat-number {
        font-size: 2rem;
    }
    .stat-item {
        padding: 5px;
    }
}
</style>

<script>
/* --- ANIMASI STATS --- */
document.addEventListener('DOMContentLoaded', function() {
    const statValues = document.querySelectorAll('.stat-value');
    let animated = false;
    
    function animateNumbers() {
        if (animated) return;
        
        statValues.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            const duration = 4000;
            const fps = 60;
            const totalFrames = duration / (1000 / fps);
            const increment = target / totalFrames;
            let current = 0;
            let frame = 0;
            
            function updateNumber() {
                frame++;
                current += increment;
                
                if (frame < totalFrames) {
                    stat.textContent = Math.floor(current);
                    requestAnimationFrame(updateNumber);
                } else {
                    stat.textContent = target;
                }
            }
            
            updateNumber();
        });
        
        animated = true;
    }
    
    const statsSection = document.getElementById('section2');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumbers();
            }
        });
    }, { threshold: 0.3 });
    
    observer.observe(statsSection);
});
</script>