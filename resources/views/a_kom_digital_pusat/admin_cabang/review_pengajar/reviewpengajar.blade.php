@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Daftar Review Pengajar</h5>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($groupedReviews->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comment-dots fa-3x mb-3"></i>
                            <p>Belum ada review yang tersedia.</p>
                        </div>
                    @else
                        @php
                            if (!function_exists('renderStars')) {
                                function renderStars($value)
                                {
                                    $stars = 0;

                                    switch ($value) {
                                        case 'sangat_baik':
                                            $stars = 5;
                                            break;
                                        case 'baik':
                                            $stars = 4;
                                            break;
                                        // Tambahkan lagi jika ada level lain, misalnya:
                                        case 'cukup':
                                            $stars = 3;
                                            break;
                                    }

                                    if ($stars > 0) {
                                        return '<span class="text-warning">' .
                                            str_repeat('<i class="bi bi-star-fill"></i>', $stars) .
                                            '</span>';
                                    }

                                    // Default: tampilkan teks biasa
                                    return ucfirst(str_replace('_', ' ', $value));
                                }
                            }
                        @endphp


                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table table-striped text-truncate">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Modul</th>
                                        <th>Kelas Tutorial</th>
                                        <th>Nama Pengajar</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedReviews as $group)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $group['modul'] }}</td>
                                            <td>{{ $group['kelas_tutorial'] }}</td>
                                            <td>{{ $group['pengajar'] }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal" data-bs-target="#detailReviewModal{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Modal Detail Review (di luar struktur tabel, satu modal per kelas tutorial) --}}
                        @foreach ($groupedReviews as $group)
                                        <div class="modal fade" id="detailReviewModal{{ $loop->index }}" tabindex="-1"
                                            role="dialog" aria-labelledby="detailReviewModalLabel{{ $loop->index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="detailReviewModalLabel{{ $loop->index }}">
                                                            Detail Review - {{ $group['kelas_tutorial'] }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-4"><strong>Modul</strong><div>{{ $group['modul'] }}</div></div>
                                                            <div class="col-md-4"><strong>Kelas Tutorial</strong><div>{{ $group['kelas_tutorial'] }}</div></div>
                                                            <div class="col-md-4"><strong>Nama Pengajar</strong><div>{{ $group['pengajar'] }}</div></div>
                                                        </div>
                                                        <hr>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped">
                                                                <thead class="thead-light">
                                                                    <tr class="text-center">
                                                                        <th class="text-nowrap">Nama Peserta</th>
                                                                        <th class="text-nowrap">Isi Pengajaran</th>
                                                                        <th class="text-nowrap">Kebenaran Teologis</th>
                                                                        <th class="text-nowrap">Menyampaikan Seluruh Materi</th>
                                                                        <th class="text-nowrap">Menyampaikan Materi Utama</th>
                                                                        <th class="text-nowrap">Pembukaan Materi</th>
                                                                        <th class="text-nowrap">Kesinambungan Materi</th>
                                                                        <th class="text-nowrap">Kesaksian Pribadi</th>
                                                                        <th class="text-nowrap">Materi mudah dipahami</th>
                                                                        <th class="text-nowrap">Sesuai dengan Materi</th>
                                                                        <th class="text-nowrap">Tanggal Review</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($group['reviews'] as $review)
                                                                        <tr class="text-center">
                                                                            <td class="text-nowrap">{{ $review->user->name ?? '-' }}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->isi_pengajaran) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->kebenaran_teologis) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->pengajar_sampaikan_seluruh_materi) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->pengajar_sampaikan_materi_utama) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->pembukaan_materi) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->kesinambungan_materi) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->kesaksian_pribadi) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->materi_dipahami) !!}</td>
                                                                            <td class="text-nowrap">{!! renderStars($review->sesuai_dengan_materi) !!}</td>
                                                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
