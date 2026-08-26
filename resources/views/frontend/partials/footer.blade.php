<!-- SECTION 8: FOOTER -->
<footer id="section8" class="footer-section py-5">
  <div class="container">
    <!-- Separator -->
    <hr class="footer-separator my-5">
    
    <!-- Baris Bawah: Links dan Copyright -->
    <!-- Menggunakan justify-content-between untuk desktop: Kiri - Kanan -->
    <!-- Menggunakan flex-wrap untuk mobile: elements akan turun ke bawah jika sempit -->
    <div class="row flex-wrap justify-content-md-between justify-content-center text-center gap-4 gap-md-0">
      
      <!-- Kiri: Policy Links -->
      <!-- Di mobile: order 2 (bawah), Di desktop: order 1 (kiri) -->
      <div class="col-12 col-md-auto order-2 order-md-1 d-flex justify-content-center align-items-center footer-policy-links">
        <a href="{{ route('privacy') }}" class="footer-link">Privacy & Policy</a>
      </div>
      
      <!-- Kanan: Social Icons + Copyright -->
      <!-- Di mobile: order 1 (atas), Di desktop: order 2 (kanan) -->
      <div class="col-12 col-md-auto order-1 order-md-2 d-flex justify-content-center align-items-center gap-4 footer-right-content">
        <div class="footer-socials">
          <a href="#" aria-label="LinkedIn" class="footer-social-icon">
            <i class="bi bi-linkedin" style="font-size: 1.5rem;"></i>
          </a>
          <a href="#" aria-label="YouTube" class="footer-social-icon">
            <i class="bi bi-youtube" style="font-size: 1.5rem;"></i>
          </a>
        </div>
        <small class="footer-copyright">2026 © BY <a href="https://gbigatsu.id">GBIGATSU</a></small>
      </div>
      
    </div>
  </div>
</footer>

<!-- Floating WhatsApp Button -->
<div class="floating-whatsapp">
  <a href="https://wa.me/628197888877" target="_blank" class="floating-whatsapp-btn" aria-label="Hubungi via WhatsApp">
    <!-- Ganti SVG dengan Image sesuai request sebelumnya, eller bisa pake SVG original jika gambarnya tidak ada -->
    <img src="{{ asset('frontend/wa1.png') }}" alt="WhatsApp" class="floating-whatsapp-img">
  </a>
</div>

<style>
/* --- FOOTER SECTION --- */
.footer-section {
  background-color: #fcf9f4; /* Cream/Whiteish background */
  color: #292524; /* Dark text */
  font-family: 'Space Grotesk', sans-serif !important;
}

/* Separator Line */
.footer-separator {
  max-width: 1140px;
  width: 100%;
  border: 0;
  border-top: 3px solid #221f1c; /* Sedikit lebih gelap dari teks utama agar kontras */
  opacity: 0.8;
  margin-left: auto;
  margin-right: auto;
}

/* Wrapper kanan (Socials + Copyright) */
.footer-right-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

/* Policy Links */
.footer-policy-links {
  font-family: 'Space Grotesk', sans-serif !important;
}

.footer-link {
  font-family: 'Space Grotesk', sans-serif !important;
  font-weight: 600;
  font-size: 0.875rem;
  color: #6c6763; /* Abu-abu gelap */
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #70A1E3; /* Biru muda seperti header */
  text-decoration: underline;
}

/* Social Icons */
.footer-socials {
  display: flex;
  align-items: center;
  gap: 16px;
}

.footer-social-icon {
  color: #6c6763;
  transition: color 0.3s ease;
}

.footer-social-icon:hover {
  color: #70A1E3;
}

/* Copyright */
.footer-copyright {
  font-family: 'Space Grotesk', sans-serif !important;
  font-weight: 600;
  font-size: 0.875rem;
  color: #6c6763;
}

.footer-copyright a {
  color: #6c6763;
  text-decoration: none;
}

.footer-copyright a:hover {
  color: #70A1E3;
}


/* --- FLOATING WHATSAPP --- */
.floating-whatsapp {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 9999;
}

.floating-whatsapp-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #25D366 0%, #20BD5A 100%);
  color: #ffffff !important;
  border-radius: 50%;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(37, 211, 102, 0.5);
}

.floating-whatsapp-btn:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 30px rgba(37, 211, 102, 0.7);
  color: #ffffff !important;
}

.floating-whatsapp-img {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

/* Animation Pulse */
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
  70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
  100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
}

.floating-whatsapp-btn {
  animation: pulse 2s infinite;
}


/* --- RESPONSIVE (HP) --- */
@media (max-width: 768px) {
  .footer-separator {
    border-top-width: 2px;
  }
  
  .footer-section {
    padding-top: 3rem !important;
    padding-bottom: 5rem !important; /* Ruang bawah agar tidak ketutup WhatsApp */
  }

  /* Di HP, kita buat layoutnya vertikal/center semua */
  .footer-right-content {
    margin-bottom: 1rem;
  }
  
  .footer-policy-links {
    margin-top: 0.5rem;
  }
  
  .footer-copyright {
    width: 100%; /*-supaya text-align center works well*/
    display: block;
  }
  
  /* WhatsApp lebih kecil dan rapat di pojok */
  .floating-whatsapp {
    bottom: 20px;
    right: 20px;
  }
  
  .floating-whatsapp-btn {
    width: 54px;
    height: 54px;
  }
  
  .floating-whatsapp-img {
    width: 28px;
    height: 28px;
  }
}
</style>