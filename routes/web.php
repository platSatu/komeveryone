<?php

use App\Http\Controllers\Kmd\Peserta\SertifikatController;
use App\Http\Controllers\Kmd\Superadmin\KmdPertanyaanPesertaController;
use App\Http\Controllers\Kmd\Superadmin\KmdUserClassTutorialController;
use App\Http\Controllers\KomDigital\DashboardKomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kmd\AdminCabang\AdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\ClassTutorialAdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\DataStatikController;
use App\Http\Controllers\Kmd\AdminCabang\DonasiAdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\RekeningAdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\ReviewPengajarAdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\TarikSaldoAdminCabangController;
use App\Http\Controllers\Kmd\AdminCabang\UserAdminCabangController;
// Diklat tutor 
use App\Http\Controllers\Kmd\AdminTutor\TutorClassTutorialAdminCabangController;
use App\Http\Controllers\Kmd\DiklatTutor\DiklatPertanyaanUserController;

//use App\Http\Controllers\Kmd\Dashboard\DashboardController;
use App\Http\Controllers\Kmd\Dashboard\KmdVideoLearningController;
use App\Http\Controllers\Kmd\Dashboard\PersembahanController;
use App\Http\Controllers\Kmd\Dashboard\PertanyaanUserController;
use App\Http\Controllers\Kmd\Dashboard\ZoomMeetingController;
use App\Http\Controllers\Kmd\Dashboard\ZoomSdkController;
use App\Http\Controllers\Kmd\Pengajar\PengajarController;
use App\Http\Controllers\Kmd\Peserta\PesertaController;
use App\Http\Controllers\Kmd\Tutor\TutorController;
use App\Http\Controllers\Kmd\Superadmin\AnswerController;
use App\Http\Controllers\Kmd\Superadmin\BookController;
use App\Http\Controllers\Kmd\Superadmin\CertificatesController;
use App\Http\Controllers\Kmd\Superadmin\ClassTutorialController;
use App\Http\Controllers\Kmd\Superadmin\DonasiController;
use App\Http\Controllers\Kmd\Superadmin\InsertDataUserMassalController;
use App\Http\Controllers\Kmd\Superadmin\ModulController;
use App\Http\Controllers\Kmd\Superadmin\ProgressModulController;
use App\Http\Controllers\Kmd\Superadmin\QuestionController;
use App\Http\Controllers\Kmd\Superadmin\QuestionVideosController;
use App\Http\Controllers\Kmd\Superadmin\TarikSaldoController;
use App\Http\Controllers\Kmd\Superadmin\UserQuestionController;
use App\Http\Controllers\Kmd\Superadmin\UserRekeningController;
use App\Http\Controllers\Kmd\Superadmin\UserVideoProgressController;
use App\Http\Controllers\Kmd\Superadmin\VideoController;
use App\Http\Controllers\KomAdminCabangController;
use App\Http\Controllers\KomAdminController;
use App\Http\Controllers\KomDigital\AuthController;

use App\Http\Controllers\KomDigital\KelasTutorialController;
use App\Http\Controllers\KomDigital\KomActivityLogController;
use App\Http\Controllers\KomDigital\KomBookController;
use App\Http\Controllers\KomDigital\KomCabangController;
use App\Http\Controllers\KomDigital\KomCertificateController;
use App\Http\Controllers\KomDigital\KomClassBookingController;
use App\Http\Controllers\KomDigital\KomClassController;
use App\Http\Controllers\KomDigital\KomDonasiController;
use App\Http\Controllers\KomDigital\KomJawabanController;
use App\Http\Controllers\KomDigital\KomModulController;
use App\Http\Controllers\KomDigital\KomPaymentController;
use App\Http\Controllers\KomDigital\KomPertanyaanController;
use App\Http\Controllers\KomDigital\KomPertanyaanUserController;
use App\Http\Controllers\KomDigital\KomUserJawabanPertanyaanController;
use App\Http\Controllers\KomDigital\KomUserModulProgressController;
use App\Http\Controllers\KomDigital\KomUserProgressController;
use App\Http\Controllers\KomDigital\KomUserVideoProgressController;
use App\Http\Controllers\KomDigital\KomVideoController;
use App\Http\Controllers\KomDigital\KomVideoPertanyaanController;
use App\Http\Controllers\KomDigital\RayonController;
use App\Http\Controllers\KomDigital\ScheduleController;
use App\Http\Controllers\Kmd\Dashboard\ValidasiKodeBukuController;
use App\Http\Controllers\Kmd\Dashboard\BahasaController;
use App\Http\Controllers\Kmd\Dashboard\KatalogKelasController;
use App\Http\Controllers\Kmd\Superadmin\KomUserController;
use App\Http\Controllers\Kmd\Superadmin\VisitorStatisticController;
use App\Http\Controllers\Kmd\Superadmin\NginxLogStatisticController;
use App\Http\Controllers\Superadmin\ClassController;
use App\Http\Controllers\Kmd\AdminCabang\AbsensiController;
use App\Http\Controllers\FrontendController;


Route::get('/', [FrontendController::class, 'index']);
Route::get('/privacy-policy', [FrontendController::class, 'privacy'])->name('privacy');

// Route jembatan untuk QR sertifikat LAMA yang masih mengarah ke prefix URL
// lama "/kom-digital/verifikasi/{id}" (sebelum diganti ke "/app/verifikasi/{id}"
// di PesertaController::sertifikatSaya() / TutorController::sertifikatSaya()).
// QR code sertifikat digenerate sekali lalu disimpan sebagai file PNG statis
// (lihat file_exists($filePath) check di kedua method itu) — jadi sertifikat
// yang sudah dicetak/didownload sebelum perbaikan ini TIDAK ikut ter-update
// otomatis, dan URL lama di gambar QR-nya harus tetap hidup selamanya, bukan
// cuma sementara sampai semua sertifikat lama "kadaluarsa". Berdiri sendiri di
// luar Route::prefix('app') di bawah supaya path-nya benar-benar "/kom-digital/
// verifikasi/{id}" dari root, bukan "/app/kom-digital/verifikasi/{id}".
Route::get('/kom-digital/verifikasi/{id}', [SertifikatController::class, 'verifikasi']);

Route::prefix('app')->group(function () {
    Route::get('syarat-dan-ketentuan', [AuthController::class, 'SyaratKetentuan'])->name('kom.snk');
    // Route untuk Register
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('kom.register');
    Route::get('register_tutor', [AuthController::class, 'showRegisterFormTutor'])->name('kom.register.tutor');
    Route::get('register_pengajar', [AuthController::class, 'showRegisterFormPengajar'])->name('kom.register.pengajar');
    Route::post('/cek-sim', [AuthController::class, 'cekSim'])->name('cek.sim');
    Route::post('register', [AuthController::class, 'register'])->name('kom_register');
    Route::post('registerTutor', [AuthController::class, 'registerTutor'])->name('kom_registerTutor');
    Route::get('verify/{token}', [AuthController::class, 'verifyEmail'])->name('kom.users.verify');
    // Route untuk Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('kom.login');
    Route::post('login', [AuthController::class, 'login'])->name('kom_login');

    Route::get('/resend-verification/{email}', [AuthController::class, 'resendVerification'])
        ->name('kom.resend_verification');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('kom.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('kom.password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('kom.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('kom.password.update');

    // Route untuk Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('kom.logout');

    Route::get('/verifikasi/{id}', [SertifikatController::class, 'verifikasi']);
    // kom_v02 (keamanan): sebelumnya bisa dibuka siapa saja tanpa login padahal mengubah data user
    Route::get('/generate-nik-kmd', [AuthController::class, 'generateNikUntukSemuaUser'])->middleware(['cekrolekom:superadmin']);

    // routes/web.php

    // Route dengan autentikasi khusus 'auth.kom'
    // kom_v02: 'kom.bahasa' = bahasa tampilan mengikuti bahasa belajar user + wajib pilih bahasa (user baru)
    Route::middleware(['kom.session', 'userprogress', 'kom.bahasa'])->name('kom.')->group(function () {
        Route::get('kom/profile', [AuthController::class, 'profileKom'])->name('profile_kom');
        Route::post('/kom/profile/update', [AuthController::class, 'updateProfile'])->name('profile_kom.update');
        // Dashboard Kom Digital
        Route::get('dashboard', [DashboardKomController::class, 'index'])->name('dashboard_kom');

        //Route::get('kom-100', [DashboardKomController::class, 'Kom100'])->name('kom100');
        // Route untuk melihat modul berikutnya
        //Route::get('/kom/modul/{id}', [DashboardKomController::class, 'showModul'])->name('modul.show');
        // Route untuk validasi jawaban
        //Route::post('/kom/validate/answer', [DashboardKomController::class, 'validateAnswer'])->name('validate.answer');
        //Route::get('/kom/video-question/{video}', [KomController::class, 'getVideoQuestion']);
        //Route::get('/kom/pertanyaan/{videoId}', [DashboardKomController::class, 'getPertanyaanByVideo']);
        //Route::post('/kom/validate', [DashboardKomController::class, 'validateMultipleAnswers'])->name('validate.multiple');
        //Route::post('/kom/jawaban', [DashboardKomController::class, 'simpanJawaban']);
        //Route::post('/kom/simpan-progress', [DashboardKomController::class, 'simpanProgressVideo']);


        Route::prefix('validasi')->controller(ValidasiKodeBukuController::class)->name('validasi.')->group(function () {
            Route::get('/no_buku', 'formValidate')->name('form_validasi');
            Route::post('/no_buku/validate', 'validateKodeBuku')->name('no_buku.validate');
        });

        // ===================== kom_v02: Bahasa Belajar & Pilih Kelas =====================
        Route::prefix('bahasa-belajar')->controller(BahasaController::class)->name('bahasa.')->group(function () {
            Route::get('/', 'index')->name('index');     // Pilih Bahasa Belajar
            Route::post('/', 'store')->name('store');
        });

        Route::prefix('pilih-kelas')->controller(KatalogKelasController::class)->name('katalog.')->group(function () {
            Route::get('/', 'index')->name('index');                                   // Katalog KOM 100/200/300/400
            Route::get('/{kode}', 'masuk')->name('masuk');                             // Masuk ke kelas yang sudah didaftar
            Route::get('/{kode}/kode-buku', 'kodeBuku')->name('kode_buku');            // Form kode buku
            Route::post('/{kode}/kode-buku', 'simpanKodeBuku')->name('kode_buku.store');
        });
        // ================================================================================

        Route::prefix('kelas/kom-100')->controller(KmdVideoLearningController::class)->name('kelas_lecture.')
            ->middleware(['kelaskom'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get-video-questions/{videoId}', 'getVideoQuestions')->name('get_video_questions'); // Rute untuk mengambil pertanyaan video
                Route::post('/submit-answer', 'submitAnswer')->name('submit_answer');
                Route::post('/save-watch-time', 'saveWatchTime')->name('video.saveWatchTime');
                Route::post('/next-video', 'getNextVideo')->name('next-video');
                Route::get('/tutorial', 'kelasTutorial')->name('tutorial');
            });

        // Route::prefix('kom-100/kelas-tutorial')->controller(\App\Http\Controllers\Kmd\Dashboard\KelasTutorialController::class)->name('kelas_tutorial.')->group(function () {
        //     Route::get('/', 'index')->name('index');
        //     Route::post('/join/kelas-tutorial', 'joinClass')->name('join');
        // });

        //Route::get('/dummy/public/kom-digital/kom-100/kelas-tutorial/pertanyaan', [PertanyaanUserController::class, 'formPertanyaan'])->name('pertanyaan.form');
        //Route::post('/dummy/public/kom-digital/kom-100/kelas-tutorial/pertanyaan/store', [PertanyaanUserController::class, 'storePertanyaan'])->name('pertanyaan-kom-100.store');
        // Route::prefix('/kom-100/kelas-tutorial')
        //     ->middleware(['auth:kom', 'userprogress'])
        //     ->group(function () {
        //         Route::get('/pertanyaan', [PertanyaanUserController::class, 'formPertanyaan'])->name('pertanyaan.form');
        //         Route::post('/pertanyaan/store', [PertanyaanUserController::class, 'storePertanyaan'])->name('pertanyaan-kom-100.store');
        //     });
        Route::prefix('kom-100/kelas-tutorial')
            ->controller(\App\Http\Controllers\Kmd\Dashboard\KelasTutorialController::class)
            ->name('kelas_tutorial.')
            ->middleware(['auth:web'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/join/kelas-tutorial', 'joinClass')->name('join');

                Route::get('/pertanyaan', [PertanyaanUserController::class, 'formPertanyaan'])->name('pertanyaan.form');
                Route::post('/pertanyaan-kom-100/store', [PertanyaanUserController::class, 'storePertanyaan'])->name('pertanyaan-kom-100.store');
            });



        Route::get('/kom-digital/kom-100/persembahan', [PersembahanController::class, 'formPersembahan'])->name('form.persembahan');
        Route::post('/kom-digital/kom-100/persembahan', [PersembahanController::class, 'storePersembahan'])->name('persembahan.store');
        Route::post('/persembahan/token', [PersembahanController::class, 'generateSnapToken'])->name('persembahan.token');
        Route::post('/persembahan/batal', [PersembahanController::class, 'batalkanPendaftaran'])->name('persembahan.batal'); // kom_v02
        Route::post('/persembahan/cek', [PersembahanController::class, 'cekPembayaran'])->name('persembahan.cek'); // kom_v02: status BB (Bizbos)
        Route::post('/persembahan/store', [PersembahanController::class, 'storePersembahan'])->name('kom.persembahan.store');


        Route::prefix('kom-100/diklat-tutor')
            ->controller(\App\Http\Controllers\Kmd\DiklatTutor\DiklatKelasTutorialController::class)
            ->name('diklat_tutor.')
            ->middleware(['auth:web'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/join/diklat-tutor', 'joinClassDiklat')->name('joinDiklat');

                Route::get('/pertanyaan', [DiklatPertanyaanUserController::class, 'formPertanyaan'])->name('pertanyaan.form');
                Route::post('/pertanyaan-kom-100/store', [DiklatPertanyaanUserController::class, 'storePertanyaan'])->name('pertanyaan-kom-100.store');
            });



        Route::get('/kom-digital/kom-100/persembahan', [PersembahanController::class, 'formPersembahan'])->name('form.persembahan');
        Route::post('/kom-digital/kom-100/persembahan', [PersembahanController::class, 'storePersembahan'])->name('persembahan.store');
        Route::post('/persembahan/token', [PersembahanController::class, 'generateSnapToken'])->name('persembahan.token');
        Route::post('/persembahan/store', [PersembahanController::class, 'storePersembahan'])->name('kom.persembahan.store');

        Route::prefix('peserta')
            ->controller(PesertaController::class)
            ->name('peserta.')
            ->middleware(['auth:web', 'cekrolekom:peserta,superadmin,pengajar,admin_cabang'])
            ->group(function () {
                Route::get('/kelas_tutorial', 'kelas_tutorial')->name('kelas_tutorial');
                Route::get('/review/{id}', 'formReviewPengajar')->name('formReviewPeserta');
                Route::post('/storeReview', 'storeReview')->name('storeReviewPeserta');
                Route::get('/pertanyaan', 'pertanyaanSaya')->name('pertanyaan');
                Route::get('pertanyaan/{id}/edit', 'editPertanyaanSaya')->name('pertanyaan.edit');
                Route::put('pertanyaan/{id}', 'updatePertanyaanSaya')->name('pertanyaan.update');
                Route::get('/persembahan', 'persembahanSaya')->name('persembahan');
                Route::get('/buku', 'bukuSaya')->name('buku');
                Route::get('/progress_kom', 'progressSaya')->name('progress_kom');
                Route::get('/e-sertifikat', 'sertifikatSaya')->name('sertifikat');
                Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('meetings.join');
                Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');
                Route::get('/sertifikat/download', 'downloadSertifikat')->name('sertifikat.download');
                // Route::get('/verifikasi/{token}', 'verifikasi')->name('verifikasi'); ;
            });

        Route::prefix('zoom')->name('zoom.')->group(function () {
            Route::get('/', [ZoomMeetingController::class, 'index'])->name('index')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::get('/create', [ZoomMeetingController::class, 'create'])->name('create')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::post('/', [ZoomMeetingController::class, 'store'])->name('store')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::get('/{meetingId}/edit', [ZoomMeetingController::class, 'edit'])->name('edit')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::put('/{meetingId}', [ZoomMeetingController::class, 'update'])->name('update')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::delete('/{meetingId}', [ZoomMeetingController::class, 'destroy'])->name('destroy')->middleware('cekrolekom:admin_cabang,superadmin'); // kom_v02 (keamanan)
            Route::get('/zoom-view', [ZoomSdkController::class, 'index'])->name('zoom.sdk.index');
            Route::post('/zoom-sdk-signature', [ZoomSdkController::class, 'generateSignature'])->name('zoom.sdk.signature');
            // Halaman join meeting (terima zoom_meeting_id)
            Route::get('/zoom/meetings/{zoomMeetingId}/join', [ZoomMeetingController::class, 'join'])->name('zoom.meetings.join');

            // Endpoint generate signature Zoom SDK
            Route::post('/zoom/meetings/signature', [ZoomMeetingController::class, 'generateSignature'])->name('zoom.meetings.signature');
        });


        // Route::prefix('pengajar')->controller(PengajarController::class)->name('pengajar.')->middleware(['auth:kom', 'cekrolekom:pengajar,superadmin'])->group(function () {
        //     Route::get('/kelas_tutorial', 'index')->name('index');
        //     Route::get('/pertanyaan', 'pertanyaan')->name('pertanyaan');
        //     Route::get('/kelas-tutorial', 'kelasTutorial')->name('kelas-tutorial');
        //     Route::get('/kelas-tutorial/{kelasId}/detail', 'detailKelas')->name('detail-kelas');
        //     Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('meetings.join');
        //     Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');
        // });
        Route::prefix('pengajar')
            ->name('pengajar.')
            ->middleware(['cekrolekom:pengajar,superadmin'])
            ->group(function () {

                // Route untuk PengajarController
                Route::controller(PengajarController::class)->group(function () {
                    Route::get('/kelas_tutorial', 'index')->name('index');
                    Route::get('/pertanyaan', 'pertanyaan')->name('pertanyaan');
                    Route::get('/kelas-tutorial', 'kelasTutorial')->name('kelas-tutorial');
                    Route::get('/kelas-tutorial/{kelasId}/detail', 'detailKelas')->name('detail-kelas');
                    Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('meetings.join');
                    Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');
                });

                Route::controller(TutorController::class)
                    ->name('tutor.')
                    ->middleware(['auth:web', 'cekrolekom:superadmin,pengajar,admin_cabang'])
                    ->group(function () {
                        Route::get('/diklat_saya', 'kelas_tutorial')->name('diklat_saya');
                        Route::get('/review/{id}', 'formReviewPengajar')->name('formReviewPeserta');
                        Route::post('/storeReview', 'storeReview')->name('storeReviewPeserta');
                        Route::get('/pertanyaan', 'pertanyaanSaya')->name('pertanyaan');
                        Route::get('/persembahan', 'persembahanSaya')->name('persembahan');
                        Route::get('/buku', 'bukuSaya')->name('buku');
                        Route::get('/progress_kom', 'progressSaya')->name('progress_kom');
                        Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('meetings.join');
                        Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');
                        Route::get('/sertifikat/download', 'downloadSertifikat')->name('sertifikat.download');
                        // Route::get('/verifikasi/{token}', 'verifikasi')->name('verifikasi'); ;
                    });

            });


        // kom_v02 (keamanan): SELURUH menu admin cabang wajib login sebagai admin_cabang / superadmin
        // (sebelumnya sebagian besar hanya butuh login — peserta bisa membuka lewat link)
        Route::prefix('admin_cabang')->middleware(['cekrolekom:admin_cabang,superadmin'])->group(function () {
            Route::prefix('users')->controller(UserAdminCabangController::class)->name('admin_cabang_users.')->middleware(['auth:web', 'cekrolekom:admin_cabang,superadmin'])->group(function () {
                Route::get('/', 'index')->name('index');                     // Tampilkan semua user
                Route::get('/create', 'create')->name('create');             // Form tambah user
                Route::post('/', 'store')->name('store');                    // Simpan user baru
                Route::get('/{id}/edit', 'edit')->name('edit');              // Form edit user (pakai UUID terenkripsi)
                Route::put('/{id}', 'update')->name('update');               // Update user
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle_status');
                Route::get('/users/{id}/detail', 'detail')->name('detail');
                Route::get('/pengajar', 'pengajar')->name('pengajar');
            });


            Route::prefix('kelas_tutorial')->controller(ClassTutorialAdminCabangController::class)->name('kelas_tutor_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');             // Form tambah user
                Route::post('/', 'store')->name('store');                    // Simpan user baru
                Route::get('/{id}/edit', 'edit')->name('edit');              // Form edit user (pakai UUID terenkripsi)
                Route::put('/{id}', 'update')->name('update');               // Update user
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('/kelas-tutorial/{id}/end-meeting', 'endMeeting')->name('end-meeting');
                Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('join');
                Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');

                Route::get('/{id}/absensi', 'absensi')->name('absensi');
                Route::get('/{id}/review', 'review')->name('review');
                Route::post('{id}/absensi', 'absensiStore')->name('absensi.store');
                Route::post('{id}/review', 'reviewStore')->name('review.store');

                Route::get('{id}/pertanyaan', 'showPertanyaan')->name('showPertanyaan');
                Route::get('{id}/persembahan', 'showDonasi')->name('showDonasi');
                Route::get('{id}/showReview', 'showReview')->name('showReview');

                //Route::get('/addPesertaZoom', 'addPesertaZoom')->name('addPesertaZoom');
                //Route::post('/{kelasId}/add-peserta', 'addPesertaZoomSubmit')->name('addPesertaZoom.submit');
                Route::post('/{kelasId}/add-peserta', 'addPesertaZoomSubmit')->name('addPesertaZoom.submit');
                Route::get('/{kelasId}/add-peserta', 'addPesertaZoom')->name('addPesertaZoom'); // untuk form

                Route::get('/api/pengajars', 'getPengajarsByModul')->name('api.pengajars.byModul');
                Route::get('/api/titik-tutorial', 'getTitikByModul')->name('api.titik.byModul'); // kom_v02
            });

            Route::prefix('data-statik')->controller(DataStatikController::class)->name('data-statik.')->group(function () {
                Route::get('/', 'index')->name('index');
            });

            Route::prefix('persembahan')->controller(DonasiAdminCabangController::class)->name('persembahan_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/tarik-dana', 'tarikDana')->name('tarik-saldo-cabang');
                Route::get('/testDonasiPolos', 'testDonasiPolos')->name('testDonasiPolos');
            });

            // 

            Route::prefix('laporan-persembahan')->controller(TarikSaldoAdminCabangController::class)->name('laporan_persembahan_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
            });

            Route::prefix('review-pengajar')->controller(ReviewPengajarAdminCabangController::class)->name('review-pengajar_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/getAllReviews', 'showAll')->name('getAllReviews');
            });

            Route::prefix('absensi')->controller(AbsensiController::class)->name('absensi.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::put('/update/{id}', 'update')->name('update');
                Route::delete('/destroy/{id}', 'destroy')->name('destroy');

                Route::get('per-kelas', 'perKelas')->name('per-kelas');
                Route::get('summary', 'summary')->name('summary');
                Route::get('export', 'export')->name('export');
            });

            Route::prefix('tarik-saldo')->controller(TarikSaldoAdminCabangController::class)->name('tarik-saldo_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/tarik', 'tarikSaldo')->name('tarik');
            });

            Route::prefix('rekening')->controller(RekeningAdminCabangController::class)->name('rekening_cabang.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/verifikasi', 'verifikasi')->name('verifikasi');
            });
        });

        // kom_v02 (keamanan): SELURUH menu superadmin wajib login sebagai superadmin
        Route::prefix('superadmin')->middleware(['cekrolekom:superadmin'])->group(function () {
            // ===================== kom_v02: Bahasa, Kelas, Terjemahan Tampilan =====================
            Route::middleware(['auth:web', 'cekrolekom:superadmin'])->group(function () {
                Route::prefix('bahasa')->controller(\App\Http\Controllers\Kmd\Superadmin\BahasaController::class)->name('super_bahasa.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/tambah', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{language}/ubah', 'edit')->name('edit');
                    Route::put('/{language}', 'update')->name('update');
                    Route::post('/{language}/aktif', 'toggle')->name('toggle');
                    Route::post('/salin-konten', 'salin')->name('salin');
                    Route::post('/{language}/hapus-konten', 'hapusKonten')->name('hapus_konten'); // kom_v02
                    Route::delete('/{language}', 'destroy')->name('destroy');                       // kom_v02
                    Route::post('/kelola', 'kelola')->name('kelola');
                });

                Route::prefix('kelola-kelas')->controller(\App\Http\Controllers\Kmd\Superadmin\KelolaKelasController::class)->name('super_kelola_kelas.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/tambah', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{kelas}/ubah', 'edit')->name('edit');
                    Route::put('/{kelas}', 'update')->name('update');
                });

                Route::prefix('terjemahan-tampilan')->controller(\App\Http\Controllers\Kmd\Superadmin\TerjemahanTampilanController::class)->name('super_terjemahan.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'simpan')->name('simpan');
                    Route::post('/pindai', 'pindai')->name('pindai');
                });
            });
            // =========================================================================================

            Route::prefix('users')->controller(KomUserController::class)->middleware(['auth:web', 'cekrolekom:superadmin'])->name('super_user.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::get('/asal-rayon', 'asalRayon')->name('asalRayon'); // kom_v02: daftar CK/Rayon lewat server (cache)
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::put('/{encryptedId}/status', 'updateStatus')->name('updateStatus');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('reset/{id}', 'resetUser')->name('resetUser');
                Route::put('/mass-action', 'massAction')->name('massAction');
                Route::post('import', 'import')->name('import');
                Route::get('/createBanyak', 'createBanyak')->name('createBanyak');
            });

            Route::prefix('diklat_tutor')->controller(TutorClassTutorialAdminCabangController::class)->name('kelas_tutor_diklat.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');             // Form tambah user
                Route::post('/', 'store')->name('store');                    // Simpan user baru
                Route::get('/{id}/edit', 'edit')->name('edit');              // Form edit user (pakai UUID terenkripsi)
                Route::put('/{id}', 'update')->name('update');               // Update user
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('/kelas-tutorial/{id}/end-meeting', 'endMeeting')->name('end-meeting');
                Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('join');
                Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');

                Route::get('/{id}/absensi', 'absensi')->name('absensi');
                Route::get('/{id}/review', 'review')->name('review');
                Route::post('{id}/absensi', 'absensiStore')->name('absensi.store');
                Route::post('{id}/review', 'reviewStore')->name('review.store');

                Route::get('{id}/pertanyaan', 'showPertanyaan')->name('showPertanyaan');
                Route::get('{id}/persembahan', 'showDonasi')->name('showDonasi');
                Route::get('{id}/showReview', 'showReview')->name('showReview');

                //Route::get('/addPesertaZoom', 'addPesertaZoom')->name('addPesertaZoom');
                //Route::post('/{kelasId}/add-peserta', 'addPesertaZoomSubmit')->name('addPesertaZoom.submit');
                Route::post('/{kelasId}/add-peserta', 'addPesertaZoomSubmit')->name('addPesertaZoom.submit');
                Route::get('/{kelasId}/add-peserta', 'addPesertaZoom')->name('addPesertaZoom'); // untuk form


            });

            // Route::prefix('data-statik')->controller(TutorDataStatikController::class)->name('data-statik.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            // });

            // Route::prefix('persembahan')->controller(TutorDonasiAdminCabangController::class)->name('persembahan_cabang.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            //     Route::post('/tarik-dana', 'tarikDana')->name('tarik-saldo-cabang');
            //     Route::get('/testDonasiPolos', 'testDonasiPolos')->name('testDonasiPolos');
            // });


            // Route::prefix('laporan-persembahan')->controller(TutorTarikSaldoAdminCabangController::class)->name('laporan_persembahan_cabang.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            // });

            // Route::prefix('review-pengajar')->controller(TutorReviewPengajarAdminCabangController::class)->name('review-pengajar_cabang.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            //     Route::get('/getAllReviews', 'showAll')->name('getAllReviews');
            // });

            // Route::prefix('absensi')->controller(TutorAbsensiController::class)->name('absensi.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            //     Route::get('/create', 'create')->name('create');
            //     Route::post('/store', 'store')->name('store');
            //     Route::get('/edit/{id}', 'edit')->name('edit');
            //     Route::put('/update/{id}', 'update')->name('update');
            //     Route::delete('/destroy/{id}', 'destroy')->name('destroy');

            //     Route::get('per-kelas', 'perKelas')->name('per-kelas');
            //     Route::get('summary', 'summary')->name('summary');
            //     Route::get('export', 'export')->name('export');
            // });

            // Route::prefix('tarik-saldo')->controller(TutorTarikSaldoAdminCabangController::class)->name('tarik-saldo_cabang.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            //     Route::post('/tarik', 'tarikSaldo')->name('tarik');
            // });

            // Route::prefix('rekening')->controller(TutorRekeningAdminCabangController::class)->name('rekening_cabang.')->group(function () {
            //     Route::get('/', 'index')->name('index');
            //     Route::post('/store', 'store')->name('store');
            // });

            Route::prefix('inputMassal')->controller(InsertDataUserMassalController::class)->name('inputMassal.')->group(function () {
                Route::get('/', 'indexBanyak')->name('index');
                Route::get('/create', 'createBanyak')->name('create');
                Route::post('/create', 'storeMassal')->name('store');
            });

            Route::prefix('kelas-tutorial')->controller(ClassTutorialController::class)->name('super_kelas_tutorial.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/zoom/meetings/{zoomMeetingId}/join', 'join')->name('meetings.join');
                Route::post('/zoom/meetings/signature', 'generateSignature')->name('meetings.signature');
            });

            Route::prefix('daftar-pertanyaan')->controller(QuestionController::class)->name('super_pertanyaan.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                // Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('import', 'importForm')->name('import_form');
                Route::post('import', 'importExcel')->name('import_excel');
            });

            Route::prefix('daftar-jawaban')->controller(AnswerController::class)->name('super_jawaban.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('setting-pertanyaan-video')->controller(QuestionVideosController::class)->name('super_pertanyaan-video.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('progress-video-peserta')->controller(UserVideoProgressController::class)->name('super_progress-video.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('progress-modul-peserta')->controller(ProgressModulController::class)->name('super_progress-modul.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('jawaban-user')->controller(UserQuestionController::class)->name('super_jawaban-user.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('daftar-rayon')->controller(\App\Http\Controllers\Kmd\Superadmin\CabangController::class)->name('super_daftar-rayon.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('books')->controller(BookController::class)->name('super_books.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('e-sertifikat')->controller(CertificatesController::class)->name('super_e-sertifikat.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('kom-modul')->controller(ModulController::class)->name('super_kom-modul.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('videos')->controller(VideoController::class)->name('super_videos.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('persembahan')->controller(DonasiController::class)->name('super_persembahan.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/ajax/users', 'search')->name('ajax.users.search');

            });

            Route::prefix('kelas-kom')->controller(\App\Http\Controllers\Kmd\Superadmin\ClassController::class)->name('super_kelas-kom.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('daftar-rekening')->controller(UserRekeningController::class)->name('super_daftar-rekening.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });


            Route::prefix('tarik-saldo')->controller(TarikSaldoController::class)->name('super_tarik-saldo.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/{id}/kirim', 'kirim')->name('kirim');
                Route::post('/{id}/cek', 'cek')->name('cek');
                Route::post('/{id}/tolak', 'tolak')->name('tolak');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('user-class-tutorial')->controller(KmdUserClassTutorialController::class)->name('super_userclasstutorial.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            Route::prefix('pertanyaan-user')->controller(KmdPertanyaanPesertaController::class)->name('super_pertanyaan_user.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            // Ditambahkan 8 September 2026 (permintaan user): halaman
            // Statistik Pengunjung (filter minggu/bulan/tahun + halaman
            // paling banyak dikunjungi), data dari App\Models\VisitorHistory.
            Route::prefix('visitor-statistik')->controller(VisitorStatisticController::class)->middleware(['auth:web', 'cekrolekom:superadmin'])->name('super_visitor.')->group(function () {
                Route::get('/', 'index')->name('index');
            });

            // Ditambahkan 8 September 2026 (permintaan user): halaman Statistik
            // Pengunjung versi log nginx (sumber beda dari VisitorStatisticController
            // di atas -- ini dari tabel nginx_access_logs hasil import access.log nginx,
            // lihat App\Console\Commands\ImportNginxLog).
            Route::prefix('nginx-log-statistik')->controller(NginxLogStatisticController::class)->middleware(['auth:web', 'cekrolekom:superadmin'])->name('super_nginxlog.')->group(function () {
                Route::get('/', 'index')->name('index');
            });

            // kom_v02: Admin KOM — setting payment gateway (Midtrans / BB Bizbos)
            Route::prefix('payment-gateway')->controller(\App\Http\Controllers\Kmd\Superadmin\PaymentGatewayController::class)->middleware(['auth:web', 'cekrolekom:superadmin'])->name('super_payment_gateway.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::put('/', 'update')->name('update');
                Route::post('/tes-bizbos', 'tesBizbos')->name('tes_bizbos');
            });

            // kom_v02: Admin KOM — review semua pengajar (semua cabang)
            Route::prefix('review-pengajar')->controller(\App\Http\Controllers\Kmd\Superadmin\ReviewPengajarController::class)->middleware(['auth:web', 'cekrolekom:superadmin'])->name('super_review_pengajar.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{pengajarId}', 'show')->whereUuid('pengajarId')->name('show');
            });

        });



        // Route::controller(KomBookController::class)->group(function () {
        //     Route::get('/kode_buku/validate', 'formValidate')->name('kode_buku.formValidate');
        //     Route::post('/kode_buku/validate', 'validateKodeBuku')->name('kode_buku.validate');
        //     Route::get('/kode_buku', 'index')->name('kode_buku.index');
        //     Route::get('/kode_buku/create', 'create')->name('kode_buku.create');
        //     Route::post('/kode_buku/store', 'store')->name('kode_buku.store');
        //     Route::get('/kode_buku/{id}/edit', 'edit')->name('kode_buku.edit'); // Rute untuk halaman edit
        //     Route::put('/kode_buku/{id}/update', 'update')->name('kode_buku.update'); // Rute untuk update data
        //     Route::delete('/kode_buku/{id}/delete', 'destroy')->name('kode_buku.destroy');
        //     Route::patch('/kom-buku/{id}/update-status', [KomBookController::class, 'updateStatus'])->name('kode_buku.update_status');

        // });

        // Route::controller(KomClassController::class)->group(function () {
        //     Route::get('/kom_class', 'index')->name('kom_class.index');
        //     Route::get('/kom_class/create', 'create')->name('kom_class.create');
        //     Route::post('/kom_class/store', 'store')->name('kom_class.store');
        //     Route::get('/kom_class/{id}/edit', 'edit')->name('kom_class.edit'); // Rute untuk halaman edit
        //     Route::put('/kom_class/{id}/update', 'update')->name('kom_class.update'); // Rute untuk update data
        //     Route::delete('/kom_class/{id}/delete', 'destroy')->name('kom_class.destroy');
        // });

        // Route::controller(KomCertificateController::class)->group(function () {
        //     Route::get('/kom_certificates', 'index')->name('certificates.index');
        //     Route::get('/kom_certificates/create', 'create')->name('certificates.create');
        //     Route::post('/kom_certificates/store', 'store')->name('certificates.store');
        //     Route::get('/kom_certificates/{id}/edit', 'edit')->name('certificates.edit'); // Rute untuk halaman edit
        //     Route::put('/kom_certificates/{id}/update', 'update')->name('certificates.update'); // Rute untuk update data
        //     Route::delete('/kom_certificates/{id}/delete', 'destroy')->name('certificates.destroy');
        // });

        // Route::controller(RayonController::class)->group(function () {
        //     Route::get('/rayon', 'index')->name('rayon.index');
        //     Route::get('/rayon/create', 'create')->name('rayon.create');
        //     Route::post('/rayon/store', 'store')->name('rayon.store');
        //     Route::get('/rayon/{id}/edit', 'edit')->name('rayon.edit'); // Rute untuk halaman edit
        //     Route::put('/rayon/{id}/update', 'update')->name('rayon.update'); // Rute untuk update data
        //     Route::delete('/rayon/{id}/delete', 'destroy')->name('rayon.destroy');
        // });
        // Modul
        // Route::controller(KomModulController::class)->group(function () {
        //     Route::get('/modul', 'index')->name('modul.index');
        //     Route::get('/modul/create', 'create')->name('modul.create');
        //     Route::post('/modul/store', 'store')->name('modul.store');
        //     Route::get('/modul/{id}/edit', 'edit')->name('modul.edit'); // Rute untuk halaman edit
        //     Route::put('/modul/{id}/update', 'update')->name('modul.update'); // Rute untuk update data
        //     Route::delete('/modul/{id}/delete', 'destroy')->name('modul.destroy');
        // });

        // Video
        // Route::controller(KomVideoController::class)->group(function () {
        //     Route::get('/video', 'index')->name('video.index');
        //     Route::get('/video/create', 'create')->name('video.create');
        //     Route::post('/video/store', 'store')->name('video.store');
        //     Route::get('/video/{id}/edit', 'edit')->name('video.edit'); // Rute untuk halaman edit
        //     Route::put('/video/{id}/update', 'update')->name('video.update'); // Rute untuk update data
        //     Route::delete('/video/{id}/delete', 'destroy')->name('video.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomPertanyaanController::class)->group(function () {
        //     Route::get('/pertanyaan', 'index')->name('pertanyaan.index');
        //     Route::get('/pertanyaan/create', 'create')->name('pertanyaan.create');
        //     Route::post('/pertanyaan/store', 'store')->name('pertanyaan.store');
        //     Route::get('/pertanyaan/{id}/edit', 'edit')->name('pertanyaan.edit'); // Rute untuk halaman edit
        //     Route::put('/pertanyaan/{id}/update', 'update')->name('pertanyaan.update'); // Rute untuk update data
        //     Route::delete('/pertanyaan/{id}/delete', 'destroy')->name('pertanyaan.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomJawabanController::class)->group(function () {
        //     Route::get('/jawaban', 'index')->name('jawaban.index');
        //     Route::get('/jawaban/create', 'create')->name('jawaban.create');
        //     Route::post('/jawaban/store', 'store')->name('jawaban.store');
        //     Route::get('/jawaban/{id}/edit', 'edit')->name('jawaban.edit'); // Rute untuk halaman edit
        //     Route::put('/jawaban/{id}/update', 'update')->name('jawaban.update'); // Rute untuk update data
        //     Route::delete('/jawaban/{id}/delete', 'destroy')->name('jawaban.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomVideoPertanyaanController::class)->group(function () {
        //     Route::get('/video_pertanyaan', 'index')->name('video_pertanyaan.index');
        //     Route::get('/video_pertanyaan/create', 'create')->name('video_pertanyaan.create');
        //     Route::post('/video_pertanyaan/store', 'store')->name('video_pertanyaan.store');
        //     Route::get('/video_pertanyaan/{id}/edit', 'edit')->name('video_pertanyaan.edit'); // Rute untuk halaman edit
        //     Route::put('/video_pertanyaan/{id}/update', 'update')->name('video_pertanyaan.update'); // Rute untuk update data
        //     Route::delete('/video_pertanyaan/{id}/delete', 'destroy')->name('video_pertanyaan.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomUserVideoProgressController::class)->group(function () {
        //     Route::get('/user_video_progress', 'index')->name('user_video_progress.index');
        //     Route::get('/user_video_progress/create', 'create')->name('user_video_progress.create');
        //     Route::post('/user_video_progress/store', 'store')->name('user_video_progress.store');
        //     Route::get('/user_video_progress/{id}/edit', 'edit')->name('user_video_progress.edit'); // Rute untuk halaman edit
        //     Route::put('/user_video_progress/{id}/update', 'update')->name('user_video_progress.update'); // Rute untuk update data
        //     Route::delete('/user_video_progress/{id}/delete', 'destroy')->name('user_video_progress.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomUserJawabanPertanyaanController::class)->group(function () {
        //     Route::get('/user_jawaban_pertanyaan', 'index')->name('user_jawaban_pertanyaan.index');
        //     Route::get('/user_jawaban_pertanyaan/create', 'create')->name('user_jawaban_pertanyaan.create');
        //     Route::post('/user_jawaban_pertanyaan/store', 'store')->name('user_jawaban_pertanyaan.store');
        //     Route::get('/user_jawaban_pertanyaan/{id}/edit', 'edit')->name('user_jawaban_pertanyaan.edit'); // Rute untuk halaman edit
        //     Route::put('/user_jawaban_pertanyaan/{id}/update', 'update')->name('user_jawaban_pertanyaan.update'); // Rute untuk update data
        //     Route::delete('/user_jawaban_pertanyaan/{id}/delete', 'destroy')->name('user_jawaban_pertanyaan.destroy');
        // });

        // Pertanyaan
        // Route::controller(KomUserModulProgressController::class)->group(function () {
        //     Route::get('/user_modul_progress', 'index')->name('user_modul_progress.index');
        //     Route::get('/user_modul_progress/create', 'create')->name('user_modul_progress.create');
        //     Route::post('/user_modul_progress/store', 'store')->name('user_modul_progress.store');
        //     Route::get('/user_modul_progress/{id}/edit', 'edit')->name('user_modul_progress.edit'); // Rute untuk halaman edit
        //     Route::put('/user_modul_progress/{id}/update', 'update')->name('user_modul_progress.update'); // Rute untuk update data
        //     Route::delete('/user_modul_progress/{id}/delete', 'destroy')->name('user_modul_progress.destroy');
        // });

        // Pertanyaan
        // kom_v02 (keamanan): kelola user lama, donasi lama & log aktivitas → hanya superadmin
        Route::controller(AuthController::class)->middleware(['cekrolekom:superadmin'])->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::get('/users/create', 'create')->name('users.create');
            Route::post('/users/store', 'store')->name('users.store');
            Route::get('/users/{id}/edit', 'edit')->name('users.edit'); // Rute untuk halaman edit
            Route::put('/users/{id}/update', 'update')->name('users.update'); // Rute untuk update data
            Route::delete('/users/{id}/delete', 'destroy')->name('users.destroy');
            Route::patch('/users/{id}/status', [AuthController::class, 'updateStatus'])->name('users.updateStatus');
        });

        // Pertanyaan
        // Route::controller(ScheduleController::class)->group(function () {
        //     Route::get('/kelas_tutorial', 'kelasTutorial')->name('kelas_tutor.index');

        //     Route::get('/schedule', 'index')->name('schedules.index');
        //     Route::get('/schedule/rayon', 'indexForUser')->name('schedulesbyuser.index');
        //     Route::get('/schedule/create', 'create')->name('schedules.create');
        //     Route::post('/schedule/store', 'store')->name('schedules.store');
        //     Route::get('/schedule/{id}/edit', 'edit')->name('schedules.edit'); // Rute untuk halaman edit
        //     Route::put('/schedule/{id}/update', 'update')->name('schedules.update'); // Rute untuk update data
        //     Route::delete('/schedule/{id}/delete', 'destroy')->name('schedules.destroy');
        // });

        // Booking Kelas
        // Route::controller(KelasTutorialController::class)->group(function () {

        //     Route::get('/kelas_tutorial_online', 'index')->name('kelas_tutorial.index');
        //     Route::get('/kelas_tutorial_online/create', 'create')->name('kelas_tutorial.create');
        //     Route::post('/kelas_tutorial_online/store', 'store')->name('kelas_tutorial.store');
        //     Route::get('/kelas_tutorial_online/{id}/edit', 'edit')->name('kelas_tutorial.edit'); // Rute untuk halaman edit
        //     Route::put('/kelas_tutorial_online/{id}/update', 'update')->name('kelas_tutorial.update'); // Rute untuk update data
        //     Route::delete('/kelas_tutorial_online/{id}/delete', 'destroy')->name('kelas_tutorial.destroy');
        // });

        // Route::controller(KomPertanyaanUserController::class)->group(function () {

        //     Route::get('/Pertanyaan_peserta', 'index')->name('pertanyaan_peserta.index');
        //     Route::get('/Pertanyaan_peserta/create/{id}', 'create')->name('pertanyaan_peserta.create');
        //     Route::post('/Pertanyaan_peserta/store', 'store')->name('pertanyaan_peserta.store');
        //     Route::get('/Pertanyaan_peserta/{id}/edit', 'edit')->name('pertanyaan_peserta.edit'); // Rute untuk halaman edit
        //     Route::put('/Pertanyaan_peserta/{id}/update', 'update')->name('pertanyaan_peserta.update'); // Rute untuk update data
        //     Route::delete('/Pertanyaan_peserta/{id}/delete', 'destroy')->name('pertanyaan_peserta.destroy');
        // });


        Route::controller(KomDonasiController::class)->middleware(['cekrolekom:superadmin'])->group(function () {

            Route::get('/donasi', 'index')->name('donasi.index');
            Route::post('/donasi/snap-token', 'getSnapToken')->name('donasi.snap.token');

            Route::get('/donasi/create', 'create')->name('donasi.create');
            Route::post('/donasi/store', 'store')->name('donasi.store');
            Route::get('/donasi/{id}/edit', 'edit')->name('donasi.edit'); // Rute untuk halaman edit
            Route::put('/donasi/{id}/update', 'update')->name('donasi.update'); // Rute untuk update data
            Route::delete('/donasi/{id}/delete', 'destroy')->name('donasi.destroy');
        });

        // Log Aktivitas User
        Route::get('log', [KomActivityLogController::class, 'index'])->name('log.index')->middleware(['cekrolekom:superadmin']);
    });
});


