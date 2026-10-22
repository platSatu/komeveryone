# RANGKUMAN PENGEMBANGAN KOMEVERYONE
## Multi Bahasa, Multi Kelas (KOM 100–400), Sesi Tutorial & Perbaikan Performa
Status: Tahap diskusi
Catatan: file ini lokal saja (diabaikan git lewat .git/info/exclude), tidak ikut di-push.

---

## A. PRINSIP UTAMA
1. Alur belajar yang sudah berjalan (KOM 100) TIDAK BERUBAH.
2. User lama otomatis: KOM 100, Bahasa Indonesia.
3. Bahasa dipilih di AWAL setiap rangkaian kelas dan TERKUNCI sampai rangkaian selesai.
4. Rangkaian berikutnya (mis. KOM 200) wajib pilih bahasa lagi (boleh berbeda).
5. Tidak bisa masuk kelas berikutnya sebelum lulus kelas sebelumnya (validasi nomor sertifikat).
6. Kelulusan tutorial ditentukan oleh ABSENSI admin cabang, BUKAN oleh Zoom selesai.

---

## B. FLOW USER
1. Pilih kelas: KOM 100 / 200 / 300 / 400
   - Kelas belum tersedia → tampil terkunci / "Segera hadir"
   - Kelas yang sedang berjalan → langsung masuk menu kelas
2. Cek prasyarat (KOM 200 ke atas)
   - Input nomor sertifikat kelas sebelumnya
   - Validasi: sertifikat ada, milik user tsb, dan dari kelas yang benar
   - Sertifikat bahasa apa pun berlaku
3. Pop-up PILIH BAHASA (hanya bahasa yang aktif)
   - Setelah dipilih → TERKUNCI sampai rangkaian kelas selesai
4. Input kode buku → cek ke API
   - Validasi: bahasa buku harus sama dengan bahasa yang dipilih
5. Menu terbuka → hanya modul sesuai KELAS + BAHASA
6. Belajar per modul:
   video → pertanyaan → jawaban benar → video berikutnya terbuka → dst.
7. Setelah jawaban benar di sebuah video → cek: apakah VIDEO INI punya Kelas Tutorial?
   (TIDAK ADA istilah "sesi" — struktur tetap: Kelas → Modul → Video → Kelas Tutorial)
   - Tidak ada → video berikutnya terbuka (seperti sekarang)
   - Ada → muncul pop-up "Ikut Kelas Tutorial" / "Lanjut menonton dulu"
     (video berikutnya TIDAK dikunci — sesuai keputusan L.7: nonton saja boleh, tanpa sertifikat)
   - Pilih ikut tutorial → user terkunci di rangkaian (pilih jadwal → pertanyaan → persembahan)
     sampai selesai, seperti middleware CheckUserClassProgress sekarang
   - Lewat titik tutorial tanpa ikut → klik video tsb lagi → pop-up pilihan tutorial muncul lagi
   - Kelas tutorial bisa di TENGAH modul (mis. setelah video 4) dan/atau di AKHIR modul
     (setelah video terakhir, = perilaku lama)
   Contoh KOM 100 – 1 modul (7 video):
     Video 1–4 → KELAS TUTORIAL → Video 5–7 → KELAS TUTORIAL → modul berikutnya
8. Ikut kelas tutorial = 1 RANGKAIAN, berlaku untuk SETIAP titik tutorial:
   pilih 1 jadwal kelas tutorial tersedia untuk titik tsb (sesuai kelas + bahasa)
   → wajib kirim 2 pertanyaan (materi sampai titik tsb)
   → persembahan (Midtrans) — SEKALI PER TITIK TUTORIAL
   → callback berhasil → kuota berkurang (dikunci, anti minus)
   → link Zoom terkirim (via queue)
9. Zoom selesai → TIDAK memicu apa pun
10. Admin cabang isi absensi:
    - HADIR → titik tutorial tsb tercatat LULUS (dihitung untuk sertifikat)
    - TIDAK HADIR → user pilih jadwal kelas tutorial lain untuk titik yang sama
      TANPA bayar lagi (persembahan sebelumnya tetap berlaku)
11. LULUS kelas = semua modul selesai + HADIR di semua kelas tutorial pada kelas tsb
    → sertifikat terbit (via queue), nomor sesuai format bahasa
    → bahasa terbuka kembali, user bisa pilih kelas berikutnya

---

## C. FLOW SUPERADMIN
1. Kelola Bahasa: tambah bahasa, aktif/nonaktif, prefix nomor sertifikat
2. Kelola Kelas: KOM 100–400, prasyarat kelas, status tersedia
3. Titik Kelas Tutorial diatur LANGSUNG DI VIDEO (keputusan 08-10-2026, tanpa menu "sesi"):
   - AKHIR MODUL = SELALU ADA Kelas Tutorial (otomatis, tidak perlu dicentang,
     tidak bisa dimatikan). "Video terakhir" dihitung otomatis dari urutan video aktif,
     jadi kalau video ditambah / urutan diubah, titik akhir ikut pindah sendiri.
   - TENGAH MODUL = opsional, bebas di video mana saja (mis. video 3 atau video 5):
     Form Tambah/Ubah Video: ☑ "Ada Kelas Tutorial setelah video ini"
     (centang tidak muncul di video terakhir karena sudah otomatis)
   - Contoh modul 7 video: centang video 4 → tutorial setelah video 4 + akhir modul (video 7)
   - Otomatis per kelas (video tiap kelas berbeda) & ikut tersalin saat Salin Konten
   - Data lama: modul yang sudah punya kelas tutorial → video terakhirnya dicentang otomatis
     (perilaku lama tetap jalan)
   - Pengaman: centang tidak bisa dilepas jika sudah ada peserta terdaftar di titik tsb
4. Kelola Modul per kelas + bahasa
5. Duplikat modul ke bahasa lain (video, soal, jawaban, kunci jawaban ikut tersalin)
6. Export Excel → isi terjemahan → Import
   - Hanya update teks berdasarkan ID, relasi video-soal-jawaban aman
   - Ada pratinjau + dibatalkan semua jika ada error
7. Unggah video versi bahasa lain (diproses di queue: kompres / HLS)

## D. FLOW ADMIN CABANG
1. Buat jadwal kelas tutorial untuk titik tertentu (mis. "Modul 1 – setelah Video 4")
   (otomatis terkait kelas + bahasa lewat modul)
2. Setelah Zoom selesai → isi absensi (Hadir / Tidak hadir)
   → absensi = pemicu lanjut video berikutnya & sertifikat

---

## E. PERUBAHAN DATABASE (garis besar)
- BARU  languages              : code, name, is_active, certificate_prefix
- BARU  kmd_user_classes       : user_id, class_id, language_code (lock), book_id,
                                 status (aktif/lulus), certificate_id
- UBAH  kmd_videos             : + has_tutorial (titik tutorial TENGAH modul; akhir modul otomatis)
- UBAH  kmd_classes            : + prerequisite_class_id
- UBAH  kmd_moduls             : + language_code
- UBAH  kmd_certificates       : + class_id, + language_code
- UBAH  kmd_class_tutorial     : + video_id (tutorial untuk titik video mana)
- UBAH  kmd_user_class_tutorial: + video_id, + status kehadiran,
                                 + boleh ganti jadwal tanpa bayar
- UBAH  kmd_pertanyaan_user    : + video_id (2 pertanyaan per titik tutorial)
- Migration user lama          : semua → KOM 100, bahasa Indonesia

---

## F. PERBAIKAN PERFORMA
1. Video dipindah ke CDN / layanan streaming (HLS) → server tidak terbebani
2. Hapus timer ganda simpan progres (sekarang 2x tiap 10 detik)
   → simpan tiap 30 detik + saat pause / selesai / tutup halaman
3. Session & cache pindah dari database ke Redis
4. Cache data soal per video

## G. PEMBAGIAN QUEUE
TIDAK pakai queue (user menunggu hasil):
- Cek kode buku, cek sertifikat prasyarat
- Video selesai → status completed → pertanyaan muncul
- Jawab pertanyaan → video berikutnya terbuka
- Buat pembayaran Midtrans
- Callback Midtrans → update status + kurangi kuota (dikunci)

PAKAI queue:
- Daftar Zoom + kirim link (dengan retry)
- Notifikasi pertanyaan ke guru
- Generate sertifikat PDF
- Proses video upload (kompres / HLS)
- Import Excel & duplikat modul (jika data besar)

---

## H. TAHAPAN PENGERJAAN
1. Branch git terpisah (versi berjalan tetap aman)
2. Database + migration user lama
3. Pilih kelas + pop-up bahasa + validasi prasyarat
4. Filter konten per kelas + bahasa
5. Sesi tutorial + absensi sebagai pemicu kelulusan
6. Fitur superadmin: bahasa, duplikat, export/import Excel
7. Terjemahan tampilan + sertifikat per bahasa
8. Perbaikan performa & queue (bisa paralel)
9. Pengujian menyeluruh sebelum rilis

---

## I. CATATAN TEKNIS DARI KODE SAAT INI
- Model aktif: app/Models/Kmd (tabel kmd_*), user = kmd_users (KomUser)
- Struktur: kmd_classes → kmd_moduls (kom_class_id) → kmd_videos (modul_id)
  → kmd_question_videos → kmd_questions → kmd_answereds
- KmdVideoLearningController::index() mengambil SEMUA modul tanpa filter kelas → perlu filter
- Route belajar masih hardcode: kelas/kom-100 → jadikan dinamis (redirect URL lama)
- Video disajikan dari public/videos (asset('videos/...'))
- dashboard/index.blade.php: saveWatchTime dipanggil 2 timer (onTimeUpdate + setInterval) tiap 10 detik
- .env: SESSION_DRIVER=database, CACHE_STORE=database, QUEUE_CONNECTION=database
- Sertifikat saat ini dipicu saat Zoom berakhir → pindahkan ke absensi admin cabang
- Logika buka video berikutnya (submitAnswer / getNextVideo) perlu cek gerbang sesi tutorial
- Kelas tutorial saat ini terkait modul_id (akhir modul) → tambah sesi_id (bisa di tengah modul)

---

## I-2. TEMUAN KODE (analisa sesi 2)
- joinClass menolak jika user sudah punya tutorial di modul yg sama → sesi 2 & daftar ulang (tidak hadir) pasti ditolak
- Pop-up tutorial hanya muncul saat modul selesai (JS handleModuleCompleted) → perlu pemicu per video
- "Modul wajib" sertifikat = modul yg kebetulan punya kelas tutorial (KmdClassTutorial) → ganti aturan sesi
- Pertanyaan ke guru dibatasi max 10 per user seumur hidup → ubah jadi 2 per sesi
- Sertifikat dicek per user saja (bukan per kelas); nomor = count()+1 (bisa dobel)
- Sertifikat di kode ini SUDAH pakai absensi hadir (cek apakah production sama)
- Kuota tutorial tidak di-lock saat callback
- KelasTutorialController::handlePaidStatus: Mail & ZoomLinkEmail belum di-import → callback pertama error, email terkirim saat Midtrans retry
- ID modul Praktikal/Doktrinal di-hardcode di ClassTutorialAdminCabangController
- Folder DiklatTutor & AdminTutor = salinan Dashboard & AdminCabang (perubahan harus 2x / refaktor ke service)
- Superadmin input konten: 5 menu terpisah (Modul, Video, Pertanyaan, Jawaban, Setting Pertanyaan-Video);
  kmd_questions tidak terikat modul/bahasa

---

## L. KEPUTUSAN TERBARU (fase pengerjaan pertama, branch: kom_v02)
1. Urutan user: PILIH BAHASA dulu (pop-up) → katalog KOM 100/200/300/400 (tampil sesuai bahasa)
   → input kode buku → menu terbuka. Bahasa terkunci sampai rangkaian selesai.
2. User lama → otomatis bahasa Indonesia, KOM 100. User yg sudah punya jejak KOM 100
   tetap terbuka tanpa input kode buku lagi.
3. Kode buku: sementara HANYA cek ada/tidak (API belum bisa membedakan buku English).
4. Video, pertanyaan, jawaban, SAMPAI TOMBOL/MENU sesuai bahasa.
5. SEMUA DINAMIS — superadmin cukup input data:
   - Dropdown "Bahasa yang sedang dikelola" di atas halaman superadmin; menu input tetap sama
   - Tombol "Salin dari Bahasa Indonesia" (struktur + relasi + kunci jawaban)
   - Usulan: form "Pertanyaan per video" (1 layar: soal + jawaban + tandai benar)
   - Halaman Kelola Bahasa + tombol Aktifkan + cek kelengkapan sebelum aktif
   - Menu "Terjemahan Tampilan" (teks tombol/menu disimpan di DB, bukan file kode)
   - Nama & deskripsi kelas (katalog) diinput per bahasa
   - Pop-up bahasa hanya menampilkan bahasa yang aktif
6. Prasyarat KOM 200 (cek sertifikat KOM 100): ikut dikerjakan, tapi di tahap paling akhir.
7. Tutorial: aturan seperti sekarang — setelah pilih ikut tutorial, user terkunci sampai rangkaian
   (pertanyaan → persembahan) selesai. Absensi diisi admin cabang saat Zoom berjalan = pemicu sertifikat.
   Nonton saja boleh lanjut tanpa sertifikat; pilihan tutorial muncul di titik sesi (mis. video 4) seperti sekarang.

## M. DATABASE & DEPLOY (kom_v02)
- Server: KOM akan jalan di VPS dengan terminal → `php artisan migrate` bisa dipakai
  (Redis / queue worker / Supervisor juga memungkinkan untuk tahap performa).
- Temuan: DB production TIDAK punya tabel `migrations`; tabel kmd_* dibuat migration project lama.
- Sudah dibuat (branch kom_v02):
  - Migration bawaan dibungkus Schema::hasTable(); down() tabel lama dikosongkan (rollback aman)
  - 2026_10_08_000000 baseline kmd_* (database/schema/kmd_baseline.sql) — hanya utk DB kosong
  - 000001 languages (id aktif+default, en nonaktif)
  - 000002 kmd_users.language_code (user lama = 'id')
  - 000003 kmd_classes: code, sort_order, prerequisite_class_id, cover_image + kmd_class_translations
  - 000004 language_code di kmd_moduls & kmd_questions; source_*_id di moduls/videos/questions/answereds
  - 000005 kmd_user_classes (backfill dari kmd_books aktif; ada sertifikat → completed)
  - 000006 kmd_certificates: class_id, language_code
  - 000007 ui_translations
  - Model baru: Language, KmdUserClass, KmdClassTranslation, UiTranslation
- Catatan kode: class_id KOM 100 di-hardcode di ValidasiKodeBukuController
  ('08a0dbf3-23f7-4b3c-8555-d458be60b632') → nanti dinamis dari kelas yg dipilih.

### Checklist rilis ke VPS
1. Backup lengkap database (struktur + data)
2. Uji dulu di salinan DB production (XAMPP): `php artisan migrate --pretend` lalu `php artisan migrate`
3. Di VPS: `php artisan down` → `git pull` → `composer install --no-dev` → `php artisan migrate --force`
   → `php artisan optimize:clear` → `php artisan up`
4. JANGAN PERNAH: migrate:fresh / migrate:refresh / migrate:reset / db:wipe di production

## J. BELUM DIPUTUSKAN
- (Terjawab) User yang sudah lewat titik sesi tanpa tutorial: klik video tsb → muncul pilihan
  ikut tutorial, sama seperti sekarang.
- API kode buku: nanti bisa membedakan buku English? (sementara cek ada/tidak saja)

## K. RIWAYAT KEPUTUSAN
- Meeting: kelas tutorial bisa di TENGAH modul (setelah video tertentu), tidak hanya
  di akhir modul. Rangkaian (pilih tutorial → 2 pertanyaan → persembahan → link)
  tetap sama dan berlaku untuk setiap sesi.

## N. TAHAP 2 — KELAS TUTORIAL DI TENGAH MODUL (dikerjakan 08-10-2026, belum diuji)
- Migration 000009: kmd_videos.has_tutorial; video_id (NULL = akhir modul) di kmd_class_tutorial,
  kmd_user_class_tutorial, kmd_pertanyaan_user; is_rebook + rebook_from_id di kmd_user_class_tutorial.
  Data lama tidak diubah (NULL = akhir modul = perilaku lama).
- TitikTutorialService: daftar titik per modul, status peserta per titik
  (belum / proses / terdaftar / tidak_hadir / hadir), syarat sertifikat.
- Superadmin: centang "Ada Kelas Tutorial setelah video ini" (ikut tersalin di Salin Konten;
  tidak bisa dilepas jika sudah ada jadwal).
- Admin cabang: pilihan "Titik Kelas Tutorial" di form jadwal; modul diberi label kelas + bahasa;
  modul/titik tidak bisa diganti jika sudah ada peserta.
- Peserta: pop-up "Ikut Kelas Tutorial / Lanjut Menonton Dulu" di video titik tengah;
  penanda Kelas Tutorial + status di daftar video; daftar jadwal difilter kelas + bahasa + titik.
- Daftar per titik (bukan per modul lagi); tidak hadir → jadwal pengganti TANPA persembahan,
  pertanyaan lama ikut dikirim ke pengajar baru.
- Pertanyaan: maks 2 per kelas tutorial (dulu 10 seumur akun).
- Sertifikat: hadir di SEMUA titik (tengah + akhir) semua modul aktif kelas+bahasa;
  sertifikat yang sudah terbit tetap tampil.
- Belum: ID modul Praktikal/Doktrinal (kualifikasi pengajar diklat) masih hardcode;
  email link Zoom untuk jadwal pengganti (link tampil di Kelas Tutorial Saya);
  route admin_cabang/kelas_tutorial belum pakai middleware auth (tahap keamanan).

## O. KEAMANAN ROUTE (08-10-2026, belum diuji)
- Temuan: grup route `kom.` hanya mewajibkan LOGIN (lewat middleware userprogress), TIDAK cek role
  → peserta yang login bisa membuka menu admin cabang & sebagian besar menu superadmin lewat link.
- Diperbaiki (cekrolekom):
  - prefix admin_cabang/*        → admin_cabang, superadmin
  - prefix superadmin/*          → superadmin
  - users.* lama, donasi.* lama, log → superadmin
  - zoom CRUD (index/create/store/edit/update/destroy) → admin_cabang, superadmin
    (join & signature Zoom tetap untuk semua yang login)
  - /app/generate-nik-kmd (dulu PUBLIK & mengubah data) → superadmin
- /api/books/no-books (PUBLIK: kode buku, nama, no SIM, no sertifikat) → middleware KomApiKey
  (header X-API-KEY = KOM_API_KEY di .env; selama KOM_API_KEY kosong masih terbuka + dicatat di log)
- PERLU DICEK: siapa pemakai /api/books/no-books (sistem lain?) sebelum KOM_API_KEY diisi.

## P. TAHAP 3 — TERJEMAHAN HALAMAN PESERTA (08-10-2026, belum diuji)
- Semua teks halaman peserta dibungkus __(): halaman belajar video (HTML + pop-up JS), daftar jadwal
  kelas tutorial, form 2 pertanyaan, persembahan, menu Peserta (Kelas Tutorial Saya, Pertanyaan Saya,
  Edit Pertanyaan, Persembahan Saya, e-Book Saya, Progress Saya, Review Pengajar, Join Zoom, E-Sertifikat),
  pesan server (controller belajar, tutorial, pertanyaan, persembahan, peserta, middleware akses).
- Teks JavaScript: partial a_kom_digital_pusat/partials/_teks_js + fungsi komT('teks', {param}).
- Tanggal memakai translatedFormat (nama bulan/hari ikut bahasa).
- UiTranslationEnglishSeeder: +297 terjemahan English bawaan (tidak menimpa yang sudah diubah).
- Perubahan kata bahasa Indonesia kecil: "Fee Admin" → "Biaya Admin", tombol "Persembahan" →
  "Berikan Persembahan", status "Paid/Pending" → "Lunas/Menunggu Pembayaran", "Join Kelas" → "Ikut Kelas",
  pesan jawaban salah tidak lagi menampilkan ID pertanyaan, pop-up akhir kurikulum menjelaskan
  sertifikat terbit setelah hadir di semua kelas tutorial, tombol sertifikat membuka halaman Sertifikat Saya.
- Belum: isi gambar/teks DI DALAM sertifikat per bahasa (Tahap 5), halaman superadmin/admin cabang/pengajar
  (dipakai tim internal, tetap bahasa Indonesia).

## Q. QUEUE EMAIL & ZOOM + KEAMANAN MIDTRANS (09-10-2026, belum diuji)
- ProsesTutorialLunasJob (app/Jobs/Kmd): pendaftaran Zoom + email terima kasih/link Zoom setelah lunas,
  dicoba ulang 5x (1, 5, 15, 30 menit). Status lunas & kuota tetap langsung (transaksi berkunci).
- handlePaidStatus($id, $donasiId = null) → dispatch job; callback Midtrans tidak lagi kirim email langsung.
- SendClassTutorialNotificationJob DIBUAT (dulu dipanggil tapi class-nya tidak ada → simpan jadwal dengan
  notifikasi bisa error). Filter peserta diperbaiki: status 'booked' + lunas (dulu 'aktif' → tidak pernah terkirim).
- Email verifikasi daftar, kirim ulang verifikasi, reset password → Mail::queue().
- KEAMANAN: server key & client key Midtrans DICATAT ke laravel.log di setiap request
  (PersembahanController, DiklatTutor\PersembahanController, KomDonasiController) → dihapus.
  Kunci dibaca dari config/midtrans.php (baru) supaya aman setelah php artisan optimize.
  TINDAK LANJUT: bersihkan laravel.log lama di VPS & pertimbangkan ganti (regenerate) server key Midtrans.
- Lokal: wajib `php artisan queue:work` (atau QUEUE_CONNECTION=sync untuk uji cepat tanpa worker).
- VPS: Supervisor menjalankan queue worker (panduan menyusul bersama Redis, PHP-FPM, MariaDB).

## R. BATAS WAKTU PERSEMBAHAN / CHECKOUT (09-10-2026, belum diuji)
- Masalah: peserta yang menutup pop-up Midtrans terkunci di halaman persembahan (booking pending tidak
  pernah dihapus) → admin hapus manual.
- Sekarang: batas 10 menit di langkah persembahan (30 menit di langkah pertanyaan), config/kom.php / .env
  (KOM_BATAS_PERSEMBAHAN_MENIT, KOM_BATAS_PERTANYAAN_MENIT, KOM_TENGGANG_CHECKOUT_MENIT).
- Lewat batas → booking + pertanyaan + persembahan belum lunas DIHAPUS, peserta kembali ke daftar jadwal.
  Jalur: middleware userprogress (saat peserta membuka halaman) + jadwal tiap menit kom:bersihkan-checkout.
- Pengaman: link Midtrans ikut kedaluwarsa (expiry); sebelum hapus cek status Midtrans → settlement =
  diproses lunas, pending (VA) = ditunggu; callback lunas tanpa data → Log::critical.
- Halaman persembahan: hitung mundur + tombol "Batalkan & pilih jadwal lain" (juga di form pertanyaan).
- Kolom baru: kmd_user_class_tutorial.checkout_expires_at (migration 000010).
