@if ($kelasTutorials->isEmpty())
    <div class="col-12">
        <div class="alert alert-info text-center">Tidak ada kelas tutorial ditemukan.</div>
    </div>
@else
    @foreach ($kelasTutorials as $kelas)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 rounded-3">
                <img src="{{ asset('kmd/kom100.jpg') }}" class="card-img-top"
                    alt="Thumbnail {{ $kelas->topic ?? 'Kelas Tutorial' }}"
                    style="object-fit: cover; height: 180px; width: 100%;">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-truncate" title="{{ $kelas->Topic ?? 'Kelas Tutorial' }}">
                        {{ $kelas->topic ?? 'Kelas Tutorial' }}
                    </h5>
                    <div class="card-text text-secondary small">

                        {{-- Modul --}}
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-book text-muted fs-6 mr-2"></i>
                            <span>{{ $kelas->modul->name ?? '-' }}</span>
                        </div>

                        {{-- Rayon --}}
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-map-marker-alt text-muted fs-6 mr-2"></i>
                            <span>{{ $kelas->rayon->name ?? '-' }}</span>
                        </div>

                        {{-- Tanggal Kelas --}}
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-calendar-alt text-muted fs-6 mr-2"></i>
                            <span>{{ \Carbon\Carbon::parse($kelas->start_time)->translatedFormat('l, d F Y H:i') }}</span>
                        </div>

                        {{-- Kuota --}}
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-users text-muted fs-6 mr-2"></i>
                            <span>Kuota Tersisa: {{ $kelas->sisa_quota }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-brands fa-whatsapp text-muted fs-6 mr-2"></i>
                            @php
                                // Ubah 08... jadi 628... jika perlu
                                $nomorWa = preg_replace(
                                    '/^0/',
                                    '62',
                                    preg_replace('/[^0-9]/', '', $kelas->no_whatsapp),
                                );
                            @endphp

                            <span>
                                No WhatsApp:
                                <a href="https://wa.me/{{ $nomorWa }}" target="_blank">
                                    {{ $kelas->no_whatsapp }}
                                </a>
                            </span>

                        </div>

                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-paper-plane text-muted fs-6 mr-2"></i>
                            <span>
                                Email:
                                <a href="mailto:{{ $kelas->email_cabang }}">
                                    {{ $kelas->email_cabang }}
                                </a>
                            </span>

                        </div>



                    </div>



                    @if ($kelas->sisa_quota > 0)
                        <form action="{{ route('kom.kelas_tutorial.join') }}" method="POST"
                            class="mt-auto joinClassForm">
                            @csrf
                            <input type="hidden" name="class_tutorial_id" value="{{ $kelas->id }}">
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>&nbsp;&nbsp; Ikut Kelas
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100 mt-auto" disabled title="Kuota kelas sudah penuh">
                            <i class="fas fa-times-circle me-2"></i> Kuota Penuh
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
