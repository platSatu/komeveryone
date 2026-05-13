@extends('layouts.kom')

@section('content')
    <!-- Toast Notifikasi -->
    <div id="toast-container"
        style="
    position: fixed;
    bottom: 70px;  /* supaya ada space dari bawah (sesuaikan dengan tinggi menu) */
    right: 20px;   /* jarak dari kanan */
    z-index: 9999;
    max-width: 320px; /* biar ga terlalu lebar */
    ">
        <div id="toast-success" class="toast bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <span id="toast-success-message">Data berhasil disimpan.</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="mb-3">Daftar Peserta Kelas Tutorial</h4>
            <p><strong>Modul :</strong> {{ $schedule->komModul->title ?? '-' }}</p>
            <p><strong>Kelas :</strong> {{ $schedule->komClass->name ?? '-' }}</p>
            <p><strong>Rayon :</strong> {{ $schedule->rayon->name ?? '-' }}</p>
            <p><strong>Waktu :</strong> {{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}</p>
            @php
                $jumlahHadir = $peserta->where('hadir', 'hadir')->count();
                $jumlahTidakHadir = $peserta->where('tidak_hadir', 'tidak_hadir')->count();
            @endphp

            <p><strong>Jumlah Hadir :</strong> {{ $jumlahHadir }}</p>
            <p><strong>Jumlah Tidak Hadir :</strong> {{ $jumlahTidakHadir }}</p>
        </div>
       <div class="d-flex gap-2 me-5" style="margin-left:20px; padding-top:20px;">

            {{-- @php
                $kelas = $schedule->kelasTutorial->first();
            @endphp

            @if ($kelas)
                <a href="{{ route('kom.review-guru.form', $schedule->id) }}" class="btn btn-warning mr-3">
                    Isi Review Pengajar
                </a>

                <a href="{{ route('kom.review-guru.index', $kelas->id) }}" class="btn btn-success">
                    Lihat Review Pengajar
                </a>
            @else
                <span class="text-danger"><p class="ml-3 pt-3">Kelas tutorial belum tersedia, review belum bisa diisi.</p></span>
            @endif --}}



            {{-- @php
                $kelas = $schedule->kelasTutorial->first();
            @endphp

            @if ($kelas)
                <a href="{{ route('kom.review-guru.index', $schedule->kelasTutorial->first()->id) }}"
                    class="btn btn-success">
                    Lihat Review Pengajar
                </a>
            @else
                <span class="text-danger">Kelas belum tersedia</span>
            @endif --}}
        </div>

        {{-- <a href="{{ route('kom.review-guru.form', $schedule->id) }}" class="btn btn-primary">
            Isi Review Pengajar
        </a> --}}

        <div class="card-body">
            @if ($peserta->isEmpty())
                <div class="alert alert-info">Belum ada peserta yang mengikuti jadwal ini.</div>
            @else
            <div class="table-responsive b-0" data-pattern="priority-columns">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kelas</th>
                            <th>Modul</th>
                            <th>Tanggal Booking</th>
                            <th>Status</th>
                            <th>Absensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peserta as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->user->username ?? '-' }}</td>
                                <td>{{ $item->user->email ?? '-' }}</td>
                                <td>{{ $item->kelas->name ?? '-' }}</td>
                                <td>{{ $item->modul->title ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_booking)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status === 'pending' ? 'warning' : 'success' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check mr-3">
                                            <input type="radio" class="form-check-input absensi-radio"
                                                name="kehadiran_{{ $item->id }}" value="hadir"
                                                data-id="{{ $item->id }}"
                                                {{ $item->hadir === 'hadir' ? 'checked' : '' }} required>
                                            <label class="form-check-label ms-1">Hadir</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input absensi-radio"
                                                name="kehadiran_{{ $item->id }}" value="tidak_hadir"
                                                data-id="{{ $item->id }}"
                                                {{ $item->tidak_hadir === 'tidak_hadir' ? 'checked' : '' }} required>
                                            <label class="form-check-label ms-1">Tidak Hadir</label>
                                        </div>
                                    </div>
                                </td>



                                <td>
                                    <form action="{{ route('kom.kelas_tutorial.updateStatus', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin membatalkan peserta ini ?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>



    <script>
        const updateAbsensiUrl = "{{ route('kom.kelas_tutorial.updateAbsensi', ['id' => '__id__']) }}";

        document.addEventListener('DOMContentLoaded', function() {
            const updateAbsensiUrl = "{{ route('kom.kelas_tutorial.updateAbsensi', ['id' => '__id__']) }}";
            const radios = document.querySelectorAll('.absensi-radio');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const pesertaId = this.dataset.id;
                    const kehadiran = this.value;

                    console.log('Radio changed:', {
                        pesertaId,
                        kehadiran
                    });

                    if (!kehadiran) {
                        console.error('Nilai kehadiran kosong');
                        return;
                    }

                    const url = updateAbsensiUrl.replace('__id__', pesertaId);

                    fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                kehadiran: kehadiran
                            })
                        })
                        .then(async response => {
                            if (!response.ok) {
                                let errMsg = 'Gagal update kehadiran';
                                try {
                                    const errorData = await response.json();
                                    console.error('Server error response:', errorData);
                                    if (errorData.message) {
                                        errMsg = errorData.message;
                                    }
                                } catch (e) {
                                    console.error('Gagal parsing JSON error:', e);
                                }
                                throw new Error(errMsg);
                            }
                            return response.json();
                        })


                        .then(data => {
                            console.log('Berhasil update:', data);
                            showSuccessToast(data.message || 'Berhasil disimpan');
                        })
                        .catch(error => {
                            console.error("Error update absensi:", error);
                            alert("Terjadi kesalahan saat update: " + error.message);
                        });
                });
            });

            function showSuccessToast(message) {
                const toastEl = document.getElementById('toast-success');
                if (!toastEl) {
                    alert(message);
                    return;
                }

                const toastMsg = document.getElementById('toast-success-message');
                toastMsg.innerText = message;

                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
@endsection
