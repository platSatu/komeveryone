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

                    @if ($reviews->isEmpty())
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
                                        <th>Kelas Tutorial</th>
                                        <th>User</th>
                                        <th>Pengajar</th>
                                        <th>Isi Pengajaran</th>
                                        <th>Kebenaran Teologis</th>
                                        <th>Menyampaikan Seluruh Materi</th>
                                        <th>Menyampaikan Materi Utama</th>
                                        <th>Pembukaan Materi</th>
                                        <th>Kesinambungan Materi</th>
                                        <th>Kesaksian Pribadi</th>
                                        <th>Materi mudah dipahami</th>
                                        <th>Sesuai dengan Materi</th>
                                        <th>Tanggal Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reviews as $review)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $review->classTutorial->topic ?? '-' }}</td>
                                            <td>{{ $review->user->name ?? '-' }}</td>
                                            <td>{{ $review->pengajar->name ?? '-' }}</td>
                                            <td class="text-center">{!! renderStars($review->isi_pengajaran) !!}</td>
                                            <td>{!! renderStars($review->kebenaran_teologis) !!}</td>
                                            <td>{!! renderStars($review->pengajar_sampaikan_seluruh_materi) !!}</td>
                                            <td>{!! renderStars($review->pengajar_sampaikan_materi_utama) !!}</td>
                                            <td>{!! renderStars($review->pembukaan_materi) !!}</td>
                                            <td>{!! renderStars($review->kesinambungan_materi) !!}</td>
                                            <td>{!! renderStars($review->kesaksian_pribadi) !!}</td>
                                            <td>{!! renderStars($review->materi_dipahami) !!}</td>
                                            <td>{!! renderStars($review->sesuai_dengan_materi) !!}</td>
                                            <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
@endsection
