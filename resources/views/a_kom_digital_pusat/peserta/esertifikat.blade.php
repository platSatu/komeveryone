@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">

            @if ($status === 'selesai' && $certificate_number)
                {{-- Jika sudah selesai dan sertifikat tersedia --}}
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="card-title">{{ __('E-Sertifikat') }}</h4>
                    </div>



                    
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

                    <!-- Tombol download -->
                    <button id="downloadPdfBtn" class="btn btn-primary btn-sm">{{ __('Download Sertifikat PDF') }}</button>

                    <!-- Sertifikat Content -->
                    <div id="sertifikatContent"
                        style="
        position: relative;
        width: 1122px;
        height: 794px;
        margin: 0 auto;
        padding: 0;
        font-family: Arial, sans-serif;
        overflow: hidden;
     ">
                        <img src="{{ asset('kmdsertifikat/sertifikat.jpg') }}" alt="Background Sertifikat"
                            style="
         position: absolute;
         top: 0;
         left: 0;
         width: 1122px;
         height: 794px;
         object-fit: cover;
         z-index: 0;
       " />

<div
        style="
            position: absolute;
            top: 288px;  /* posisi di bawah nama */
            left: 110px;
            font-size: 18px;
            color: #000;
            z-index: 1;
            white-space: nowrap;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        ">
        {{ $certificate_number ?? '-' }}
    </div>

                        <!-- Nama -->
                        <div
                            style="
                                position: absolute;
                                top: 385px;
                                left: 78px;
                                font-weight: bold;
                                font-size: 36px;
                                color: #000;
                                white-space: nowrap;
                                z-index: 1;
                            ">
                            {{ $nama ?? auth()->user()->name }}
                        </div>

                        <!-- QR Code -->
                        <div
                            style="
                                position: absolute;
                                bottom: 187px;
                                left: 83px;
                                z-index: 1;
                            ">
                            <img src="{{ $qr_link }}" alt="QR Code" width="100" height="100" />
                        </div>

                        <!-- Tanggal Buat Sertifikat -->
                        <div
                            style="
                                position: absolute;
                                top: 480px;
                                left: 80px;
                                font-size: 16px;
                                color: #000;
                                z-index: 1;
                            ">
                            Jakarta, {{ \Carbon\Carbon::parse($class_tutorial_created_at)->translatedFormat('d F Y') }}
                        </div>
                        
                    </div>

                    <!-- Script untuk generate PDF -->
                    <script>
                        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
                            const element = document.getElementById('sertifikatContent');

                            const opt = {
                                margin: [0, 0, 0, 0], // TOP, RIGHT, BOTTOM, LEFT
                                filename: 'sertifikat.pdf',
                                image: {
                                    type: 'jpeg',
                                    quality: 1
                                },
                                html2canvas: {
                                    scale: 2, // cukup 2 saja, 4 kadang bikin hasil melar
                                    useCORS: true // penting kalau gambar dari luar / asset
                                },
                                jsPDF: {
                                    unit: 'px',
                                    format: [1122, 794], // ukuran A4 landscape dalam pixel
                                    orientation: 'landscape'
                                },
                                pagebreak: {
                                    mode: ['avoid']
                                } // hindari pemecahan halaman
                            };

                            html2pdf().set(opt).from(element).save();
                        });
                    </script>








                </div>
            @else
                {{-- Jika belum selesai --}}
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h4 class="card-title">{{ __('E-Sertifikat') }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info text-center">
                            <h5><i class="mdi mdi-alert-circle-outline"></i> {{ __('E-Sertifikat Belum Tersedia') }}</h5>
                            <p>
                                {{ __('Sertifikat digital tersedia setelah Anda menyelesaikan seluruh materi dan hadir di semua kelas tutorial.') }}
                            </p>

                            @if (isset($modulSelesai) && isset($totalModul))
                                {{-- kom_v02: dihitung dari kehadiran di setiap kelas tutorial (tengah & akhir modul) --}}
                                <p class="mt-3">{{ __('Status Anda') }}: <strong>{{ $modulSelesai }}/{{ $totalModul }}
                                        {{ __('Kelas Tutorial dihadiri') }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
