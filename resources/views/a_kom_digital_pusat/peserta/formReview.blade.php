@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('kom.peserta.storeReviewPeserta') }}">
                @csrf
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('Form Review Pengajaran') }}</h5>
                    </div>
                    <div class="card-body">
                        {{-- Hidden inputs --}}
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="pengajar_id" value="{{ $pengajar_id }}">
                        <input type="hidden" name="userclasstutorial_id" value="{{ $userclasstutorial_id }}">

                        @php
                            $questions = [
                                'isi_pengajaran' => __('Bagaimana penilaian Anda terhadap isi pengajaran?'),
                                'kebenaran_teologis' => __('Apakah isi pengajaran memiliki kebenaran teologis?'),
                                'pengajar_sampaikan_seluruh_materi' => __('Apakah pengajar menyampaikan seluruh materi?'),
                                'pengajar_sampaikan_materi_utama' => __('Apakah pengajar menyampaikan materi utama?'),
                                'pembukaan_materi' => __('Apakah pembukaan materi menarik dan jelas?'),
                                'kesinambungan_materi' => __('Apakah materi disampaikan secara berkesinambungan?'),
                                'kesaksian_pribadi' => __('Apakah pengajar menyertakan kesaksian pribadi?'),
                                'materi_dipahami' => __('Apakah materi mudah dipahami?'),
                                'sesuai_dengan_materi' => __('Apakah pengajaran sesuai dengan materi yang diberikan?'),
                            ];

                            $options = [
                                1 => __('Sangat Baik'),
                                2 => __('Baik'),
                                3 => __('Cukup'),
                                4 => __('Kurang'),
                            ];
                        @endphp

                        {{-- Radio Questions --}}
                        @foreach ($questions as $field => $label)
                            <div class="mb-4">
                                <label class="form-label mb-2">{{ $label }}</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($options as $value => $text)
                                        <div class="form-check me-5 mb-3">
                                            <input class="form-check-input" type="radio" name="{{ $field }}"
                                                id="{{ $field }}_{{ $value }}" value="{{ $value }}"
                                                required>
                                            <label class="form-check-label ms-1 mr-3"
                                                for="{{ $field }}_{{ $value }}">
                                                {{ $text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> {{ __('Kirim Review') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success') || session('error') || $errors->any())
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: @json(__('Berhasil')),
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: @json(__('Gagal')),
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: @json(__('Validasi Gagal')),
                    html: `<ul style="text-align:left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>`,
                    showConfirmButton: true
                });
            @endif
        </script>
    @endif
@endsection
