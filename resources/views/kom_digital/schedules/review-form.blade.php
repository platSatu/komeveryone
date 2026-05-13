@extends('layouts.kom')

@section('content')
    <div class="row mt-4">


        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Review Pengajar</h5>
                   
                </div>
                <div class="card-body">
                    <form action="{{ route('kom.review-guru.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_tutorial_id" value="{{ $kelasTutorialId }}">
                        <input type="hidden" name="kom_schedule_id" value="{{ $scheduleId }}">

                        @php
                            $options = ['sangat_baik', 'baik', 'cukup', 'kurang'];
                            $labels = [
                                'isi_pengajaran' => 'Isi Pengajaran',
                                'kebenaran_teologis' => 'Kebenaran Teologis',
                                'pengajar_sampaikan_seluruh_materi' => 'Pengajar Menyampaikan Seluruh Materi',
                                'pengajar_sampaikan_materi_utama' => 'Pengajar Menyampaikan Materi yang Disampaikan',
                                'pembukaan_materi' => 'Pembukaan Materi',
                                'kesinambungan_materi' => 'Penjelasan/Kesinambungan Materi',
                                'kesaksian_pribadi' => 'Kesaksian Pribadi',
                                'materi_dipahami' => 'Materi Diterima dan Dipahami',
                                'sesuai_dengan_materi' => 'Pengajar Menyampaikan Sesuai dengan Materi',
                            ];
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%">Pertanyaan</th>
                                        <th class="text-center">Sangat Baik</th>
                                        <th class="text-center">Baik</th>
                                        <th class="text-center">Cukup</th>
                                        <th class="text-center">Kurang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($labels as $field => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            @foreach ($options as $option)
                                                <td class="text-center py-3" style="margin-bottom:5px;">
                                                    <input class="form-check-input" type="radio"
                                                        name="{{ $field }}" id="{{ $field . '_' . $option }}"
                                                        value="{{ $option }}" required>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label for="komentar" class="form-label">Komentar Tambahan (Opsional)</label>
                            <textarea name="komentar" id="komentar" class="form-control" rows="3"
                                placeholder="Tulis komentar tambahan di sini..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Kirim Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
