@extends('layouts.KomDigital')

@section('content')
    <div class="container py-5">
        <div class="row">
            {{-- Sidebar Profil --}}
            <aside class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        {{-- Foto profil placeholder --}}
                        <img src="{{ $data->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($data->name) }}"
                            alt="Foto {{ $data->name }}" class="rounded-circle mb-3" width="150" height="150">

                        <h3 class="mb-1">{{ $data->name ?? 'Nama tidak tersedia' }}</h3>
                        <p class="text-muted mb-2">{{ $data->role ?? '-' }}</p>

                        <p>
                            <strong>Email:</strong><br>
                            <a href="mailto:{{ $data->email }}">{{ $data->email ?? '-' }}</a>
                        </p>

                        <p>
                            <strong>Status:</strong><br>
                            @if (isset($data->status))
                                @if ($data->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($data->status) }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </p>

                        <p>
                            <strong>Cabang:</strong><br>
                            {{ $data->cabang->name ?? '-' }}
                        </p>

                        <a href="{{ route('kom.super_user.index') }}" class="btn btn-outline-secondary mt-3 w-100">Kembali
                            ke Daftar</a>
                        {{-- <a href="{{ route('kom.super_user.index') }}" class="btn btn-outline-danger mt-3 w-100">Reset
                            Data</a> --}}

                        <form action="{{ route('kom.super_user.resetUser', Crypt::encrypt($data->id)) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mereset data pengguna ini? Semua data terkait akan dihapus.');"
                            class="m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger mt-3 w-100">
                                Reset Data
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Konten Utama: Riwayat / Detail --}}
            <section class="col-lg-8">
                {{-- Progress Videos --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Progress Video ({{ $data->progressVideos->count() }})</h5>
                    @if ($data->progressVideos->isEmpty())
                        <p class="text-muted">Belum ada progress video.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->progressVideos as $video)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $video->video->name ?? 'Judul tidak tersedia' }}
                                    <span
                                        class="badge bg-primary rounded-pill text-white p1">{{ $video->status ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Kelas Tutorial ({{ $data->classTutorials->count() }})</h5>
                    @if ($data->classTutorials->isEmpty())
                        <p class="text-muted">Belum ada status tutorial.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->classTutorials as $status)
                                <li class="list-group-item d-flex justify-content-between align-items-center">

                                    {{ $status->classTutorial->topic ?? 'Tutorial' }}
                                    <span
                                        class="badge bg-primary rounded-pill text-white p1">{{ $status->status ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                {{-- Tutorial Status --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Tutorial Status ({{ $data->tutorialStatuses->count() }})</h5>
                    @if ($data->tutorialStatuses->isEmpty())
                        <p class="text-muted">Belum ada status tutorial.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->tutorialStatuses as $status)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $status->tutorial_name ?? 'Tutorial' }}
                                    <span
                                        class="badge bg-primary rounded-pill text-white p1">{{ $status->status ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Reviews --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Reviews ({{ $data->reviews->count() }})</h5>
                    @if ($data->reviews->isEmpty())
                        <p class="text-muted">Belum ada review.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->reviews as $review)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>{{ $review->title ?? 'Judul Review' }}</strong><br>
                                    <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                    <p>{{ Str::limit($review->content ?? '-', 100) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Questions --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Questions ({{ $data->questions->count() }})</h5>
                    @if ($data->questions->isEmpty())
                        <p class="text-muted">Belum ada pertanyaan.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->questions as $question)
                                <li class="list-group-item">
                                    <p>{{ $question->question->questions ?? 'Pertanyaan' }}</p>
                                    <small class="text-muted">{{ $question->created_at->format('d M Y') }}</small>
                                    <span
                                        class="badge bg-primary rounded-pill text-white p1">{{ $question->status ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Progress Modules ({{ $data->progressModules->count() }})</h5>
                    @if ($data->progressModules->isEmpty())
                        <p class="text-muted">Belum ada modul.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->progressModules as $module)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $module->modul->name ?? 'Modul' }}
                                    <span
                                        class="badge bg-primary rounded-pill text-white p1">{{ $module->status ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                {{-- Modules --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Modules ({{ $data->modules->count() }})</h5>
                    @if ($data->modules->isEmpty())
                        <p class="text-muted">Belum ada modul.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->modules as $module)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $module->module_name ?? 'Modul' }} - Status: {{ $module->status ?? '-' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Absensi --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Absensi ({{ $data->absensis->count() }})</h5>
                    @if ($data->absensis->isEmpty())
                        <p class="text-muted">Belum ada data absensi.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->absensis as $absen)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ optional($absen->date)->format('d M Y') ?: '-' }} - Status: {{ $absen->status ?? '-' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Books --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Books ({{ $data->books->count() }})</h5>
                    @if ($data->books->isEmpty())
                        <p class="text-muted">Belum ada buku.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->books as $book)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $book->title ?? 'Judul Buku' }} - Penulis: {{ $book->author ?? '-' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Pertanyaan User --}}
                <div class="mb-4">
                    <h5 class="mb-3 border-bottom pb-2">Pertanyaan User ({{ $data->pertanyaanUsers->count() }})</h5>
                    @if ($data->pertanyaanUsers->isEmpty())
                        <p class="text-muted">Belum ada pertanyaan user.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($data->pertanyaanUsers as $pertanyaan)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $pertanyaan->pertanyaan ?? 'Pertanyaan' }}<br>
                                    <small class="text-muted">{{ $pertanyaan->created_at->format('d M Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
