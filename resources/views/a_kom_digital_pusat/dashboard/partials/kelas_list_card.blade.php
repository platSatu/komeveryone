{{-- kom_v02: kartu jadwal kelas tutorial — modern & minimalis --}}
@if ($kelasTutorials->isEmpty())
    <div class="col-12">
        <div class="kom-kt-kosong">
            <div class="kom-kt-kosong-icon"><i class="far fa-calendar-times"></i></div>
            <div>{{ __('Tidak ada kelas tutorial ditemukan.') }}</div>
        </div>
    </div>
@else
    @foreach ($kelasTutorials as $kelas)
        @php
            // Ubah 08... jadi 628... jika perlu
            $nomorWa = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string) $kelas->no_whatsapp));
        @endphp
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card kom-kt-card h-100">
                <div class="kom-kt-cover">
                    <img src="{{ asset('kmd/kom100.jpg') }}"
                        alt="Thumbnail {{ $kelas->topic ?? __('Kelas Tutorial') }}">
                    <span class="kom-kt-badge"><i class="fas fa-flag-checkered mr-1"></i>{{ $kelas->label_titik }}</span>
                    @if ($kelas->sisa_quota > 0)
                        <span class="kom-kt-kuota"><i class="fas fa-users mr-1"></i>{{ __('Kuota Tersisa') }}: {{ $kelas->sisa_quota }}</span>
                    @else
                        <span class="kom-kt-kuota penuh"><i class="fas fa-times-circle mr-1"></i>{{ __('Kuota Penuh') }}</span>
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-truncate" title="{{ $kelas->topic ?? __('Kelas Tutorial') }}">
                        {{ $kelas->topic ?? __('Kelas Tutorial') }}
                    </h5>

                    <ul class="kom-kt-info">
                        <li><i class="fas fa-book"></i><span>{{ $kelas->modul->name ?? '-' }}</span></li>
                        <li><i class="fas fa-map-marker-alt"></i><span>{{ $kelas->rayon->name ?? '-' }}</span></li>
                        <li><i class="far fa-calendar-alt"></i><span>{{ \Carbon\Carbon::parse($kelas->start_time)->translatedFormat('l, d F Y H:i') }}</span></li>
                        @if (!empty($kelas->no_whatsapp))
                            <li><i class="fab fa-whatsapp"></i>
                                <span><a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener">{{ $kelas->no_whatsapp }}</a></span>
                            </li>
                        @endif
                        @if (!empty($kelas->email_cabang))
                            <li><i class="far fa-envelope"></i>
                                <span class="text-truncate"><a href="mailto:{{ $kelas->email_cabang }}">{{ $kelas->email_cabang }}</a></span>
                            </li>
                        @endif
                    </ul>

                    @if ($kelas->sisa_quota > 0)
                        <form action="{{ route('kom.kelas_tutorial.join') }}" method="POST"
                            class="mt-auto joinClassForm">
                            @csrf
                            <input type="hidden" name="class_tutorial_id" value="{{ $kelas->id }}">
                            <button type="submit" class="kom-btn-primary w-100">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Ikut Kelas') }}
                            </button>
                        </form>
                    @else
                        <button class="kom-btn-penuh w-100 mt-auto" disabled title="{{ __('Kuota kelas sudah penuh') }}">
                            <i class="fas fa-times-circle"></i> {{ __('Kuota Penuh') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
@once
    <style>
        .kom-kt-card { border:1px solid #e9eef6; border-radius:20px; overflow:hidden; background:#fff;
            box-shadow: 0 10px 28px -20px rgba(30,60,120,.45); transition: transform .2s, box-shadow .2s; }
        .kom-kt-card:hover { transform: translateY(-3px); box-shadow: 0 18px 34px -20px rgba(30,60,120,.55); }
        .kom-kt-cover { position:relative; height:170px; background:#eef2f8; }
        .kom-kt-cover img { width:100%; height:100%; object-fit:cover; display:block; }
        .kom-kt-cover::after { content:''; position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,0) 45%, rgba(16,24,40,.55) 100%); }
        .kom-kt-badge, .kom-kt-kuota {
            position:absolute; z-index:1; font-size:.72rem; font-weight:600; padding:5px 10px; border-radius:99px;
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        }
        .kom-kt-badge { top:12px; left:12px; background:rgba(255,255,255,.9); color:#2f6fed; }
        .kom-kt-kuota { bottom:12px; left:12px; background:rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.35); }
        .kom-kt-kuota.penuh { background:rgba(229,72,77,.85); border-color:transparent; }
        .kom-kt-card .card-body { padding:20px; }
        .kom-kt-card .card-title { font-weight:700; color:#1c2b43; font-size:1.1rem; margin-bottom:14px; }
        .kom-kt-info { list-style:none; padding:0; margin:0 0 18px; }
        .kom-kt-info li { display:flex; align-items:center; gap:10px; font-size:.86rem; color:#5d6a7e; padding:5px 0; min-width:0; }
        .kom-kt-info li i { width:28px; height:28px; flex:0 0 28px; border-radius:9px; background:#f2f6fc; color:#7b8fb0;
            display:flex; align-items:center; justify-content:center; font-size:.8rem; }
        .kom-kt-info a { color:#2f6fed; }
        .kom-kt-kosong { text-align:center; padding:40px 16px; color:#7b879a; background:#f7f9fc; border-radius:18px; }
        .kom-kt-kosong-icon { font-size:2rem; color:#b6c2d4; margin-bottom:8px; }
    </style>
@endonce
