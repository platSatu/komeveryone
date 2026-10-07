<!-- NAVIGATION (Header) -->
<nav class="navbar navbar-expand-lg fixed-top custom-navbar" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold font-heading" href="#">
            
            <img src="{{ asset('frontend/komeveryone_2.png') }}" alt="KOMEVERYONE" width="100" class="nav-logo logo-white" style="display: none;">
            
            <img src="{{ asset('frontend/komeveryone_2.png') }}" alt="KOMEVERYONE" width="100" class="nav-logo logo-black" style="display: none;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section1">Home</a></li>
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section2">About</a></li>
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section3">Why KOM</a></li>
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section4">Curriculums</a></li>
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section5">Tutors</a></li>
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section6">Testimonials</a></li>
                <!-- <li class="nav-item"><a class="nav-link font-body fw-medium" href="#section7">Contact</a></li> -->
                <li class="nav-item"><a class="nav-link font-body fw-medium" href="{{ route('kom.login') }}">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* --- NAVIGATION - SCROLL BACKGROUND EFFECT --- */

.custom-navbar {
    padding: 15px 0;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transition: all 0.3s ease;
    background-color: transparent;
    box-shadow: none;
}

/* Logo styling */
.custom-navbar .nav-logo {
    transition: all 0.3s ease;
}

/* Menu Links - Default (atas / transparan) */
.custom-navbar .nav-link {
    color: #000000 !important;
    /* text-shadow: 0 1px 3px rgba(0,0,0,0.4); */
    padding: 8px 16px !important;
    margin: 0 4px;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-family: 'Space Grotesk', sans-serif !important;
    font-size: 1rem;
}

.custom-navbar .nav-link:hover {
    color: #fff !important;
    background-color: rgba(255, 255, 255, 0.2);
}

.custom-navbar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.3);
    color: #fff !important;
}

/* Navbar Toggler (Burger Icon) - Default */
.custom-navbar .navbar-toggler {
    border: 2px solid #fff;
    padding: 8px 10px;
}

.custom-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23fff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* --- STATE SETELAH DI-SCROLL (BACKGROUND HITAM/GELAP) --- */

.custom-navbar.scrolled {
    background-color: #1a1a1a !important; /* Background hitam gelap */
    box-shadow: 0 2px 20px rgba(0,0,0,0.3);
    padding: 10px 0;
}

/* Logo: Putih -> Hitam saat di-scroll */
.custom-navbar.scrolled .logo-white {
    display: none !important;
}

.custom-navbar.scrolled .logo-black {
    display: block !important;
}

.custom-navbar.scrolled .nav-link {
    color: #fff !important;
    text-shadow: none;
    background-color: transparent;
}

.custom-navbar.scrolled .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
    color: #fff !important;
}

.custom-navbar.scrolled .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff !important;
}

/* Navbar Toggler - Saat di-scroll (warna putih juga agar terlihat) */
.custom-navbar.scrolled .navbar-toggler {
    border: 2px solid #fff;
}

.custom-navbar.scrolled .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23fff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* --- NAVBAR COLLAPSE AREA (Mobile) --- */
#navbarNav {
    background-color: transparent;
    transition: all 0.3s ease;
}

.custom-navbar.scrolled #navbarNav {
    background-color: #1a1a1a;
    border-radius: 0 0 12px 12px;
    margin-top: 8px;
}

@media (max-width: 991px) {
    .custom-navbar .navbar-collapse {
        background-color: transparent;
        padding: 10px;
    }
    
    .custom-navbar.scrolled .navbar-collapse {
        background-color: #1a1a1a;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    /* Default (atas) - dark background for mobile menu */
    #navbarNav {
        background-color: rgba(0, 0, 0, 0.85);
        border-radius: 12px;
        margin-top: 10px;
    }
    
    /* Text color mobile */
    .custom-navbar .nav-link {
        color: #fff !important;
    }
}
</style>

<script>
/* --- NAVBAR SCROLL EFFECT --- */
document.addEventListener('DOMContentLoaded', function() {
    const nav = document.getElementById('mainNav');
    const scrollThreshold = 50;
    
    function updateNav() {
        if (window.scrollY > scrollThreshold) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }
    
    window.addEventListener('scroll', updateNav);
    
    // Cek juga saat halaman di-load
    updateNav();
});
</script>