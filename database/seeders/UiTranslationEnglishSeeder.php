<?php

namespace Database\Seeders;

use App\Models\Kmd\UiTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * kom_v02 — Terjemahan English awal untuk teks tampilan baru
 * (Bahasa Belajar, Pilih Kelas, Kode Buku, menu samping, Beranda).
 *
 * Jalankan:  php artisan db:seed --class=UiTranslationEnglishSeeder --force
 *
 * Aman dijalankan berulang: terjemahan yang SUDAH ADA (mis. sudah diubah
 * superadmin) TIDAK ditimpa.
 */
class UiTranslationEnglishSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach ($this->english() as $key => $value) {
            $exists = DB::table('ui_translations')
                ->where('key', $key)->where('language_code', 'en')->exists();

            if (! $exists) {
                DB::table('ui_translations')->insert([
                    'id' => (string) Str::uuid(),
                    'group' => 'umum',
                    'key' => $key,
                    'language_code' => 'en',
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Cache::forget(UiTranslation::cacheKey('en'));
    }

    protected function english(): array
    {
        return [
            ':jumlah modul' => ':jumlah modules',
            'Aktifkan & Mulai Belajar' => 'Activate & Start Learning',
            'Aktivitas Belajar' => 'My Learning Activity',
            'Anda belum terdaftar di kelas mana pun. Silakan pilih kelas terlebih dahulu.' => 'You are not enrolled in any class yet. Please choose a class first.',
            'Bahasa Belajar' => 'Learning Language',
            'Bahasa belajar Anda masih terkunci karena ada kelas yang sedang berjalan. Selesaikan kelas tersebut terlebih dahulu.' => 'Your learning language is locked because you have a class in progress. Please complete that class first.',
            'Bahasa belajar Anda sedang terkunci karena kelas :kelas masih berjalan. Anda bisa memilih bahasa lain setelah kelas tersebut selesai.' => 'Your learning language is locked because :kelas is still in progress. You can choose another language once that class is completed.',
            'Bahasa belajar berhasil disimpan. Silakan pilih kelas yang ingin Anda ikuti.' => 'Your learning language has been saved. Please choose the class you would like to join.',
            'Bahasa ini dipakai untuk video, pertanyaan, dan sertifikat Anda.' => 'This language is used for your videos, questions, and certificate.',
            'Bahasa yang dipilih belum tersedia.' => 'The selected language is not available yet.',
            'Belum Bisa Dibuka' => 'Not Available Yet',
            'Belum ada bahasa yang tersedia. Silakan hubungi admin.' => 'No language is available yet. Please contact the admin.',
            'Belum punya buku? Dapatkan di Toko Areopagus:' => 'Don\'t have the book yet? Get it at Areopagus Store:',
            'Beranda' => 'Home',
            'Bergabung & dapatkan sertifikat' => 'Join & get your certificate',
            'Buka Kelas :kelas' => 'Open :kelas',
            'Buka Materi Lagi' => 'Review Materials',
            'Buka menu Pilih Kelas, pilih kelas yang ingin Anda ikuti, lalu masukkan kode yang tertera di buku Anda.' => 'Open the Choose a Class menu, pick the class you want to join, then enter the code printed in your book.',
            'Contoh: abc123' => 'Example: abc123',
            'Ganti bahasa' => 'Change language',
            'Halo,' => 'Hello,',
            'Halo, :nama!' => 'Hello, :nama!',
            'Ikuti Kelas Tutorial' => 'Join a Tutorial Class',
            'Kelas Saya' => 'My Classes',
            'Kelas Tutorial Saya' => 'My Tutorial Classes',
            'Kelas ini akan Anda ikuti dalam :bahasa. Bahasa akan terkunci sampai kelas selesai.' => 'You will take this class in :bahasa. The language will be locked until the class is completed.',
            'Kelas ini segera hadir dalam bahasa yang Anda pilih.' => 'This class is coming soon in your selected language.',
            'Keluar' => 'Log Out',
            'Kembali' => 'Back',
            'Kode Buku' => 'Book Code',
            'Kode Buku Saya' => 'My Book Codes',
            'Kode buku berhasil diaktifkan. Selamat belajar!' => 'Your book code has been activated. Happy learning!',
            'Kode buku berhasil diaktifkan. Silakan buka menu Kelas Saya untuk mulai belajar.' => 'Your book code has been activated. Open the My Classes menu to start learning.',
            'Kode buku wajib diisi.' => 'Please enter your book code.',
            'Lanjutkan' => 'Continue',
            'Lanjutkan Belajar' => 'Continue Learning',
            'Link Zoom akan dikirim ke email Anda dan muncul di menu Kelas Tutorial Saya.' => 'The Zoom link will be sent to your email and shown in the My Tutorial Classes menu.',
            'Masukkan kode yang tertera di buku Anda untuk mulai belajar.' => 'Enter the code printed in your book to start learning.',
            'Menu Utama' => 'Main Menu',
            'Mulai Kelas' => 'Start Class',
            'Mulai perjalanan belajar Anda. Selesaikan setiap kelas untuk membuka kelas berikutnya.' => 'Begin your learning journey. Complete each class to unlock the next one.',
            'Pada titik tertentu di modul, Anda akan diajak mengikuti Kelas Tutorial bersama Tutor/Pengajar.' => 'At certain points in a module, you will be invited to join a Tutorial Class with a Tutor/Teacher.',
            'Perkembangan Belajar' => 'My Progress',
            'Persembahan Saya' => 'My Offerings',
            'Pertanyaan Saya' => 'My Questions',
            'Pilih Kelas' => 'Choose a Class',
            'Pilih kelas & masukkan kode buku' => 'Choose a class & enter your book code',
            'Profil Saya' => 'My Profile',
            'Satu kode buku hanya bisa dipakai untuk satu akun.' => 'Each book code can only be used for one account.',
            'Sebelum mulai, pilih bahasa yang ingin Anda gunakan untuk belajar.' => 'Before you begin, choose the language you would like to learn in.',
            'Sedang Dipelajari' => 'In Progress',
            'Sedang memeriksa...' => 'Checking...',
            'Segera Hadir' => 'Coming Soon',
            'Selamat datang di KOM EveryONE!' => 'Welcome to KOM EveryONE!',
            'Selamat datang di KOM EveryONE. Yuk, lanjutkan perjalanan belajar Anda.' => 'Welcome to KOM EveryONE. Let\'s continue your learning journey.',
            'Selesai' => 'Completed',
            'Selesaikan :kelas terlebih dahulu untuk membuka kelas ini.' => 'Complete :kelas first to unlock this class.',
            'Sertifikat Saya' => 'My Certificates',
            'Sertifikat terbit setelah semua modul selesai dan kehadiran Anda di setiap Kelas Tutorial tercatat.' => 'Your certificate is issued once all modules are completed and your attendance at every Tutorial Class is recorded.',
            'Setelah Anda memulai sebuah kelas, bahasa akan terkunci sampai kelas tersebut selesai. Pilih dengan tenang, ya.' => 'Once you start a class, the language will be locked until that class is completed. Take your time to choose.',
            'Silakan masuk terlebih dahulu.' => 'Please log in first.',
            'Silakan pilih bahasa belajar terlebih dahulu.' => 'Please choose your learning language first.',
            'Silakan pilih salah satu bahasa.' => 'Please choose one language.',
            'Simpan Bahasa' => 'Save Language',
            'Syaratnya: kirim 2 pertanyaan sesuai materi yang Anda pelajari, lalu berikan persembahan untuk kelas tersebut.' => 'Requirements: submit 2 questions about the material you studied, then give an offering for the class.',
            'Terkunci' => 'Locked',
            'Tersedia' => 'Available',
            'Tonton setiap video materi sampai selesai, lalu jawab pertanyaannya. Jika jawaban Anda benar, video berikutnya akan terbuka.' => 'Watch each video until the end, then answer the questions. If your answers are correct, the next video will unlock.',
            'Tonton video & jawab pertanyaan' => 'Watch videos & answer questions',
            'Kode buku ini sudah aktif dan dipakai oleh akun lain.' => 'This book code is already active on another account.',
            'Kode buku ini sudah pernah digunakan.' => 'This book code has already been used.',
            'Kode buku tidak ditemukan. Periksa kembali kode yang tertera di buku Anda.' => 'Book code not found. Please check the code printed in your book.',
            'Kode buku tidak valid. Periksa kembali kode yang tertera di buku Anda.' => 'Invalid book code. Please check the code printed in your book.',
            'Layanan pengecekan kode buku sedang tidak bisa dihubungi. Silakan coba beberapa saat lagi.' => 'The book code checking service is currently unavailable. Please try again in a moment.',
            'Terjadi kendala saat memeriksa kode buku. Silakan coba lagi.' => 'Something went wrong while checking the book code. Please try again.',
            ':selesai dari :total video selesai' => ':selesai of :total videos completed',
            'Akses Cepat' => 'Quick Access',
            'Berikutnya' => 'Up Next',
            'Cara Belajar di KOM EveryONE' => 'How Learning Works at KOM EveryONE',
            'Langkah Pertama' => 'First Step',
            'Lihat Sertifikat' => 'View Certificate',
            'Link Zoom dikirim ke email Anda. Sertifikat terbit setelah semua modul selesai dan kehadiran Anda di setiap Kelas Tutorial tercatat.' => 'The Zoom link is sent to your email. Your certificate is issued once all modules are completed and your attendance at every Tutorial Class is recorded.',
            'Mulai Belajar' => 'Start Learning',
            'Pada titik tertentu di modul, Anda akan diajak mengikuti Kelas Tutorial bersama Tutor/Pengajar. Syaratnya: kirim 2 pertanyaan dan berikan persembahan.' => 'At certain points in a module, you will be invited to join a Tutorial Class with a Tutor/Teacher. Requirements: submit 2 questions and give an offering.',
            'Pilih Kelas Berikutnya' => 'Choose Your Next Class',
            'Pilih kelas, lalu masukkan kode yang tertera di buku Anda. Setelah itu video materi langsung terbuka.' => 'Choose a class, then enter the code printed in your book. The video lessons will open right away.',
            'Selamat malam' => 'Good evening',
            'Selamat pagi' => 'Good morning',
            'Selamat siang' => 'Good afternoon',
            'Selamat sore' => 'Good afternoon',
            'Selamat! Anda telah menyelesaikan :kelas.' => 'Congratulations! You have completed :kelas.',
            'Senang melihat Anda kembali. Yuk, lanjutkan belajar hari ini.' => 'Great to see you again. Let\'s keep learning today.',
            'Terima kasih sudah setia belajar. Sertifikat Anda sudah bisa dilihat, dan kelas berikutnya menanti.' => 'Thank you for learning faithfully. Your certificate is ready, and the next class awaits.',
            'Yuk, mulai kelas pertama Anda' => 'Let\'s start your first class',
            'History' => 'History',
            'Menu' => 'Menu',
            'Progress Saya' => 'My Progress',
            'Bahasa terkunci sampai kelas yang sedang berjalan selesai.' => 'The language is locked until your current class is completed.',
            'Bahasa belajar berhasil diganti.' => 'Your learning language has been changed.',
            'Pilih bahasa untuk :kelas' => 'Choose a language for :kelas',
            'Selamat, kelas sebelumnya sudah selesai! Anda boleh memakai bahasa yang sama atau memilih bahasa lain untuk kelas ini.' => 'Congratulations on completing your previous class! You may keep the same language or choose a different one for this class.',
        ];
    }
}
