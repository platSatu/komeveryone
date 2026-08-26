@extends('layouts.KomDigital')
@section('content')
<div class="row">
    <div class="card">
        <ol>
    <li>
        <h3>Cara Akses Materi Video Modul KOM 100</h3>
        <p>Untuk mengakses materi video, silahkan input kode buku. </p>

        <p>Untuk mendapatkan kode buku, silahkan membeli bukunya  di Toko Areopagus di link dibawah ini:<br>
            <!-- <a class="mr-3" href="https://www.tokopedia.com/areopagus/">Tokopedia</a> <a href="https://shopee.co.id/areopagus">Shopee</a> -->
        </p>
        <div class="store-buttons">
    <a href="https://www.tokopedia.com/areopagus/" target="_blank" class="store-btn tokopedia">
        <i class="bi bi-bag"></i>
        <span>Tokopedia</span>
    </a>
    <a href="https://shopee.co.id/areopagus" target="_blank" class="store-btn shopee">
        <i class="bi bi-bag-fill"></i>
        <span>Shopee</span>
    </a>
</div>

<style>
.store-buttons {
    display: flex;
    gap: 1vw;
    margin-top: 1vh;
    flex-wrap: wrap;
}

.store-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5vw;
    padding: 0.8vh 1.5vw;
    border-radius: 0.8vw;
    text-decoration: none;
    font-size: 1.2vw;
    font-weight: 600;
    transition: all 0.3s ease;
    color: #fff;
}

.store-btn:hover {
    transform: translateY(-0.2vh);
    box-shadow: 0.4vw 0.4vw 1vw rgba(0,0,0,0.3);
}

.store-btn.tokopedia {
    background: linear-gradient(135deg, #00AA18 0%, #00C853 100%);
    border: 0.2vw solid #00AA18;
}

.store-btn.tokopedia:hover {
    background: linear-gradient(135deg, #00C853 0%, #00E676 100%);
}

.store-btn.shopee {
    background: linear-gradient(135deg, #FF5722 0%, #FF7043 100%);
    border: 0.2vw solid #FF5722;
}

.store-btn.shopee:hover {
    background: linear-gradient(135deg, #FF7043 0%, #FF8A65 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .store-buttons {
        flex-direction: column;
        gap: 1vh;
    }
    .store-btn {
        justify-content: center;
        font-size: 2vw;
        padding: 1vh 2vw;
    }
}
</style>
            
    </li>
    <li>
        <p>
            <h3>Cara Menonton Materi Video Modul</h3>
            <p>Jika Anda sudah input kode buku, maka akses materi video modul akan terbuka.</p>

            <p>Silahkan klik video materi modul yang tersedia, selesaikan menonton video dengan baik. Setiap Anda selesai menonton, maka akan diberikan 3 pertanyaan. Jika jawaban Anda benar, maka video materi selanjutnya akan terbuka.</p>

            <p>Selamat memulai perjalanan pembelajaran Anda di
            KOM EveryONE.</p>

        </p>
    </li>
    <li>
        <h3>Cara Mengikuti Kelas Tutorial</h3>
        <p>Setelah Anda menonton video terakhir di setiap modul, contoh : Materi Modul 110.7 - Hidup Dalam Firman Tuhan.</p>

        <p>Maka akan muncul pop up, dan akan ada pilihan, silahkan klik “ ikut kelas tutorial “</p>

        <p>Ikuti langkah-langkah yang diberikan. Syarat Wajib  untuk mengikuti adalah mengirimkan 2 pertanyaan. Pertanyaan harus sesuai dengan materi modul yang Anda pelajari dan akan menjadi materi pembahasan diskusi bersama Tutor/Pengajar.</p>

        <p>Setelah itu Anda dipersilahkan untuk memberikan persembahan untuk Kelas Tutorial yang diikuti.
        </p>
    </li>    

    <li>
        <h3>Cara Bergabung di Kelas Tutorial</h3>
        <p>Setelah Anda menyelesaikan semua syarat wajib untuk mengikuti “ Kelas Tutorial “. Anda sudah berhasil mendaftar dan link zoom terkirim otomatis di menu dashboard Anda “ Kelas Tutorial “  dan dikirimkan juga ke email Anda yang terdaftar di KOM EveryONE.</p>

        <p>Selamat mengikuti dan berdiskusi dengan Tutor/Pengajar di Kelas Tutorial yang Anda ikuti.
        </p>
    </li>    
</ol
    </div>
</div>


@endsection