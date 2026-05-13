@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">

            {{-- KmdBooks --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Book</strong>
                </div>
                <div class="card-body">
                    @if ($details['books']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No Buku</th>
                                    <th>Certificate ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['books'] as $book)
                                    <tr>
                                        <td>{{ $book->no_books }}</td>
                                        <td>{{ $book->certificate_id }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Donasi</strong>
                </div>
                <div class="card-body">
                    @if ($details['donasi']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['donasi'] as $donasi)
                                    <tr>
                                        <td>{{ $donasi->amount }}</td>
                                        <td>{{ $donasi->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Pertanyaan</strong>
                </div>
                <div class="card-body">
                    @if ($details['pertanyaan_user']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Pertanyaan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['KmdPertanyaanUser'] as $pertanyaan_user)
                                    <tr>
                                        <td>{{ $pertanyaan_user->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $pertanyaan_user->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Progress Modul</strong>
                </div>
                <div class="card-body">
                    @if ($details['progress_modul']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Modul</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['progress_modul'] as $progress_modul)
                                    <tr>
                                        <td>{{ $progress_modul->modul ? $progress_modul->modul->name : 'Nama modul tidak ditemukan' }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $progress_modul->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Kelas Tutorial</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_class_tutorial']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_class_tutorial'] as $user_class_tutorial)
                                    <tr>
                                        <td>{{ $user_class_tutorial->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_class_tutorial->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Modul</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_modul']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_modul'] as $user_modul)
                                    <tr>
                                        <td>{{ $user_modul->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_modul->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Pertanyaan yang diajukan</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_questions']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_questions'] as $user_questions)
                                    <tr>
                                        <td>{{ $user_questions->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_questions->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdDonasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Review Pengajar</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_review']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_review'] as $user_review)
                                    <tr>
                                        <td>{{ $user_review->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_review->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdUserTutorialStatus --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Status Kelas Tutorial</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_tutorial_status']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_tutorial_status'] as $user_tutorial_status)
                                    <tr>
                                        <td>{{ $user_tutorial_status->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_tutorial_status->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- KmdUserVideoProgress --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Progress Video</strong>
                </div>
                <div class="card-body">
                    @if ($details['user_video_progress']->isEmpty())
                        <p class="text-muted">Tidak ada data.</p>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['user_video_progress'] as $user_video_progress)
                                    <tr>
                                        <td>{{ $user_video_progress->amount }}</td>
                                        <td>@include('a_kom_digital_pusat.admin_cabang.user.components.status-badge', ['status' => $user_video_progress->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
