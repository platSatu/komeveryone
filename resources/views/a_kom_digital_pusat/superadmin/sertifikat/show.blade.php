@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">

            @if ($certificate_number)
                {{-- Sertifikat tersedia --}}
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="card-title">E-Sertifikat</h4>
                    </div>

                    <!-- Tombol download -->
                    <button id="downloadPdfBtn" class="btn btn-primary btn-sm mt-3 mb-3">Download Sertifikat PDF</button>

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
                            style="position: absolute; top: 0; left: 0; width: 1122px; height: 794px; object-fit: cover; z-index: 0;" />

                        <!-- Nomor Sertifikat -->
                        <div style="
                            position: absolute;
                            top: 288px;
                            left: 110px;
                            font-size: 18px;
                            color: #000;
                            z-index: 1;
                            white-space: nowrap;
                            font-weight: 600;
                            font-family: 'Montserrat', sans-serif;
                        ">
                            {{ $certificate_number }}
                        </div>

                        <!-- Nama -->
                        <div style="
                            position: absolute;
                            top: 385px;
                            left: 78px;
                            font-weight: bold;
                            font-size: 36px;
                            color: #000;
                            white-space: nowrap;
                            z-index: 1;
                        ">
                            {{ $nama }}
                        </div>

                        <!-- QR Code -->
                        <div style="position: absolute; bottom: 187px; left: 83px; z-index: 1;">
                            <img src="{{ $qr_link }}" alt="QR Code" width="100" height="100" />
                        </div>

                        <!-- Tanggal Buat Sertifikat -->
                        <div style="
                            position: absolute;
                            top: 480px;
                            left: 80px;
                            font-size: 16px;
                            color: #000;
                            z-index: 1;
                        ">
                            Jakarta, {{ $tanggalPelaksanaan }}
                        </div>
                    </div>

                    <!-- Script untuk generate PDF -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
                    <script>
                        document.getElementById('downloadPdfBtn').addEventListener('click', function () {
                            const element = document.getElementById('sertifikatContent');

                            const opt = {
                                margin: [0, 0, 0, 0],
                                filename: 'sertifikat.pdf',
                                image: { type: 'jpeg', quality: 1 },
                                html2canvas: { scale: 2, useCORS: true },
                                jsPDF: { unit: 'px', format: [1122, 794], orientation: 'landscape' },
                                pagebreak: { mode: ['avoid'] }
                            };

                            html2pdf().set(opt).from(element).save();
                        });
                    </script>

                </div>
            @else
                {{-- Tidak ada sertifikat --}}
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h4 class="card-title">E-Sertifikat Tidak Ditemukan</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger text-center">
                            <h5><i class="mdi mdi-alert-circle-outline"></i> Sertifikat belum tersedia</h5>
                            <p>User belum menyelesaikan pelatihan atau sertifikat belum diterbitkan.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
