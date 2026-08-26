@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-certificate text-warning"></i> Detail Sertifikat
            </h1>
            <a href="{{ route('kom.super_e-sertifikat.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white"></i> Kembali
            </a>
        </div>

        {{-- Info User --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user"></i> Informasi Peserta
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="150"><strong>Nama</strong></td>
                                <td>: {{ $nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. Sertifikat</strong></td>
                                <td>: <span class="text-primary font-weight-bold">{{ $certificate_number }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pelaksanaan</strong></td>
                                <td>: {{ $tanggalPelaksanaan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="150"><strong>Dibuat Oleh</strong></td>
                                <td>: {{ $dibuatOleh }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Cetak</strong></td>
                                <td>: {{ $tanggalCetak }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    @if($status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $status }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wrapper Sertifikat dengan scroll horizontal untuk tampilan kecil --}}
        <div class="sertifikat-wrapper">

            {{-- Sertifikat Content --}}
            <div id="sertifikatContent" class="no-scale"
                style="
                    position: relative;
                    width: 1122px;
                    height: 794px;
                    margin: 0 auto;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    overflow: hidden;
                    background: #fff;
                ">
                
                {{-- Background Sertifikat --}}
                <img src="{{ asset('kmdsertifikat/sertifikat.jpg') }}" 
                     alt="Background Sertifikat"
                     style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 1122px;
                        height: 794px;
                        object-fit: cover;
                        z-index: 0;
                    " />

                {{-- Nomor Sertifikat --}}
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
                    {{ $certificate_number ?? '-' }}
                </div>

                {{-- Nama --}}
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

                {{-- QR Code --}}
                <div style="
                    position: absolute;
                    bottom: 187px;
                    left: 83px;
                    z-index: 1;
                ">
                    @if($qr_link)
                        <img src="{{ $qr_link }}" alt="QR Code" width="100" height="100" />
                    @else
                        <div class="text-center text-muted">
                            <small>QR tidak tersedia</small>
                        </div>
                    @endif
                </div>

                {{-- Tanggal Buat Sertifikat --}}
                <div style="
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

        </div>

        {{-- Tombol download di luar sertifikat agar tidak tergeser skala --}}
        <div class="text-right mt-3">
            <button id="downloadPdfBtn" class="btn btn-primary btn-sm">
                <i class="fas fa-download"></i> Download Sertifikat PDF
            </button>
        </div>

    </div>

    {{-- CSS Responsive --}}
    <style>
        .sertifikat-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: #f8f9fa;
            padding: 10px 0;
            border-radius: 8px;
        }

        /* Hilangkan transform scale dari elemen yang ada class no-scale */
        #sertifikatContent:not(.no-scale) {
            transition: transform 0.2s ease-in-out;
        }

        /* Berikan transform scale hanya pada tampilan, kecuali saat cetak/download */
        @media (max-width: 1200px) {
            #sertifikatContent:not(.no-scale) {
                transform: scale(0.9);
                transform-origin: top center;
            }
        }
        @media (max-width: 992px) {
            #sertifikatContent:not(.no-scale) {
                transform: scale(0.75);
                transform-origin: top center;
            }
        }
        @media (max-width: 768px) {
            #sertifikatContent:not(.no-scale) {
                transform: scale(0.55);
                transform-origin: top center;
            }
        }
        @media (max-width: 576px) {
            #sertifikatContent:not(.no-scale) {
                transform: scale(0.45);
                transform-origin: top center;
            }
        }
    </style>

    {{-- Script html2pdf --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
document.getElementById('downloadPdfBtn').addEventListener('click', async function () {

    const original = document.getElementById('sertifikatContent');

    // Clone element agar tampilan web tidak berubah
    const clone = original.cloneNode(true);

    // Bungkus clone di container sementara
    const wrapper = document.createElement('div');

    wrapper.style.position = 'fixed';
    wrapper.style.top = '-99999px';
    wrapper.style.left = '-99999px';
    wrapper.style.width = '1122px';
    wrapper.style.height = '794px';
    wrapper.style.background = '#fff';
    wrapper.style.zIndex = '-1';

    // Reset transform
    clone.style.transform = 'scale(1)';
    clone.style.transformOrigin = 'top left';
    clone.style.width = '1122px';
    clone.style.height = '794px';

    wrapper.appendChild(clone);
    document.body.appendChild(wrapper);

    const opt = {
        margin: 0,
        filename: 'sertifikat_{{ Str::slug($nama) }}.pdf',

        image: {
            type: 'jpeg',
            quality: 1
        },

        html2canvas: {
            scale: 4,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            letterRendering: true,
            width: 1122,
            height: 794,
            scrollX: 0,
            scrollY: 0
        },

        jsPDF: {
            unit: 'px',
            format: [1122, 794],
            orientation: 'landscape',
            compress: true
        }
    };

    try {

        await html2pdf()
            .set(opt)
            .from(clone)
            .save();

    } finally {

        document.body.removeChild(wrapper);

    }

});
</script>
@endsection