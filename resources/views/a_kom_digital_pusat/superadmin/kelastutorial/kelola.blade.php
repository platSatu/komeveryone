@extends('layouts.KomDigital')

@section('page_title', __('Kelas Tutorial'))

{{--
    4 Nov 2026 — Admin KOM › Kelas Tutorial (/superadmin/kelas-tutorial).
    Tampilan gaya "Kirim Ulang Email" (2 tab: Aktif / Selesai), fungsi sama dengan admin cabang
    (ClassTutorialAdminCabangController): undang peserta Zoom, pertanyaan, persembahan, absensi,
    review, peserta Zoom, edit, hapus, start / end meeting. Semua cabang, filter per rayon.
    Halaman tidak memanggil API Zoom saat dibuka (peserta Zoom diambil saat tombolnya diklik).
--}}
@section('content')
@include('a_kom_digital_pusat.katalog._style')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    <p class="kom-text mb-3">Kelas tutorial semua cabang. Kelas yang dibuat di sini tampil juga di menu admin cabangnya, dan notifikasi peserta dikirim ke admin cabang tersebut.</p>

    {{-- Filter + tambah --}}
    <div class="kt-bar mb-3">
        <form action="{{ route('kom.super_kelas_tutorial.index') }}" method="GET" class="kt-filter m-0">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="rayon_id" class="form-control" onchange="this.form.submit()" aria-label="Filter rayon">
                <option value="">Semua Rayon</option>
                @foreach ($rayons as $rayon)
                    <option value="{{ $rayon->id }}" @selected($rayonId == $rayon->id)>{{ $rayon->name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('kom.super_kelas_tutorial.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Jadwal</a>
    </div>

    {{-- Tab --}}
    <div class="kt-tabs" role="tablist">
        @foreach (['aktif' => ['Kelas Aktif', 'fa-calendar-check'], 'selesai' => ['Selesai / Nonaktif', 'fa-history']] as $kode => [$label, $ikon])
            <a href="{{ route('kom.super_kelas_tutorial.index', array_filter(['tab' => $kode, 'rayon_id' => $rayonId])) }}"
                role="tab" class="kt-tabbtn {{ $tab === $kode ? 'aktif' : '' }}" aria-selected="{{ $tab === $kode ? 'true' : 'false' }}">
                <i class="fas {{ $ikon }}"></i> {{ $label }}
                <span class="kt-count">{{ number_format($jumlah[$kode] ?? 0) }}</span>
            </a>
        @endforeach
    </div>

    <div class="kom-card mb-3">
        <div class="table-responsive">
            <table class="table table-sm kt-table mb-0">
                <thead>
                    <tr>
                        <th>Jadwal</th>
                        <th>Cabang</th>
                        <th>Pengajar</th>
                        <th>Modul</th>
                        <th>Peserta</th>
                        <th>Aksi</th>
                        <th>Zoom</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        @php
                            $id = \Illuminate\Support\Facades\Crypt::encryptString($item->id);
                            $mulai = $item->start_time ? \Carbon\Carbon::parse($item->start_time) : null;
                        @endphp
                        <tr>
                            <td>
                                <div class="kt-utama">{{ $item->topic ?? '-' }}</div>
                                <small class="text-muted">{{ $mulai ? $mulai->format('d M Y · H:i') : '-' }}</small>
                                @if ($item->status !== 'active')
                                    <span class="kt-tag abu ml-1">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $item->rayon->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->user->name ?? '-' }}</small>
                            </td>
                            <td>{{ $item->pengajar->name ?? '-' }}</td>
                            <td>
                                <div>{{ $item->modul->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->kelas->name ?? '' }}</small>
                            </td>
                            <td class="text-nowrap">
                                <div class="kt-utama">{{ $item->jml_ikut ?? 0 }}/{{ $item->quota ?? 0 }}</div>
                                <span class="kt-tag {{ ($item->sisa_quota ?? 0) > 0 ? 'hijau' : 'merah' }}">
                                    {{ ($item->sisa_quota ?? 0) > 0 ? 'Sisa '.$item->sisa_quota : 'Penuh' }}
                                </span>
                            </td>
                            <td>
                                <div class="kt-aksi">
                                    @if ($item->zoom_meeting_id)
                                        <a href="{{ route('kom.super_kelas_tutorial.addPesertaZoom', ['kelasId' => $item->id]) }}" class="kt-ikon" title="Undang Peserta Zoom"><i class="fas fa-plus"></i></a>
                                    @endif
                                    <a href="{{ route('kom.super_kelas_tutorial.showPertanyaan', $id) }}" class="kt-ikon" title="Lihat Pertanyaan Peserta"><i class="fas fa-question-circle"></i></a>
                                    <a href="{{ route('kom.super_kelas_tutorial.showDonasi', $id) }}" class="kt-ikon" title="Lihat Persembahan Peserta"><i class="fas fa-donate"></i></a>
                                    <a href="{{ route('kom.super_kelas_tutorial.absensi', $id) }}" class="kt-ikon" title="Absensi"><i class="fas fa-check"></i></a>
                                    <a href="{{ route('kom.super_kelas_tutorial.showReview', $id) }}" class="kt-ikon" title="Review"><i class="fas fa-star"></i></a>
                                    @if ($item->zoom_meeting_id)
                                        <button type="button" class="kt-ikon" title="Lihat Peserta Zoom"
                                            onclick="muatPesertaZoom(this, '{{ route('kom.super_kelas_tutorial.zoomPeserta', $id) }}')"><i class="fas fa-users"></i></button>
                                    @endif
                                    <a href="{{ route('kom.super_kelas_tutorial.edit', $id) }}" class="kt-ikon kuning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('kom.super_kelas_tutorial.destroy', $id) }}" method="POST" class="m-0"
                                        onsubmit="return confirm('Yakin ingin hapus kelas tutorial ini? Meeting Zoom-nya juga ikut dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="kt-ikon merah" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @if ($item->zoom_meeting_id)
                                    <div class="kt-aksi">
                                        @if ($item->start_url)
                                            <a href="{{ $item->start_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success" title="Mulai meeting (host)"><i class="fas fa-play"></i> Start</a>
                                        @endif
                                        <form action="{{ route('kom.super_kelas_tutorial.end-meeting', $item->id) }}" method="POST" class="m-0"
                                            onsubmit="return confirm('Yakin ingin mengakhiri meeting?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Akhiri meeting"><i class="fas fa-stop"></i> End</button>
                                        </form>
                                        <a href="{{ route('kom.super_kelas_tutorial.join', ['zoomMeetingId' => $item->zoom_meeting_id]) }}" class="btn btn-sm btn-link px-1" title="Join Zoom">
                                            <i class="fas fa-video" style="color:#2D8CFF;"></i>
                                        </a>
                                    </div>
                                @else
                                    <small class="text-muted">Belum ada meeting</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">
                            {{ $tab === 'aktif' ? 'Tidak ada kelas aktif.' : 'Tidak ada kelas yang selesai / nonaktif.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $data->links('pagination::bootstrap-4') }}</div>
    </div>
</div>

{{-- Daftar peserta Zoom (diambil saat tombol diklik) --}}
<div id="ktPesertaZoom" class="kt-overlay" hidden>
    <div class="kt-overlay-box" role="dialog" aria-modal="true" aria-labelledby="ktPesertaZoomJudul">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 id="ktPesertaZoomJudul" class="mb-0">Peserta Zoom</h5>
            <button type="button" class="btn btn-sm btn-light" onclick="tutupPesertaZoom()" aria-label="Tutup">&times;</button>
        </div>
        <ol id="ktPesertaZoomList" class="mb-0 pl-3"></ol>
    </div>
</div>

<style>
    .kt-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px; }
    .kt-filter select { min-width: 220px; }
    .kt-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
    .kt-tabbtn { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e3e8f0; background: #fff; color: #5b6576;
        border-radius: 12px; padding: 10px 16px; font-weight: 600; text-decoration: none !important; }
    .kt-tabbtn:hover { border-color: #9db8f2; color: #2a5298; }
    .kt-tabbtn.aktif { background: #2a5298; border-color: #2a5298; color: #fff; }
    .kt-count { background: #eef0f3; color: #5b6576; border-radius: 999px; padding: 1px 9px; font-size: .8rem; }
    .kt-tabbtn.aktif .kt-count { background: rgba(255, 255, 255, .2); color: #fff; }
    .kt-table td, .kt-table th { vertical-align: middle; }
    .kt-utama { font-weight: 600; color: #1e3c72; }
    .kt-tag { display: inline-block; font-size: .75rem; font-weight: 600; border-radius: 999px; padding: 2px 9px; white-space: nowrap; }
    .kt-tag.hijau { background: #e7f7ee; color: #1e7e46; }
    .kt-tag.merah { background: #fdeeee; color: #c0392b; }
    .kt-tag.abu { background: #eef0f3; color: #5b6576; }
    .kt-aksi { display: flex; flex-wrap: nowrap; gap: 4px; align-items: center; }
    .kt-ikon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px;
        border: 1px solid #b9e3ef; background: #fff; color: #17a2b8; cursor: pointer; text-decoration: none !important; }
    .kt-ikon:hover { background: #e8f7fb; }
    .kt-ikon.kuning { border-color: #f5d18a; color: #e69500; }
    .kt-ikon.kuning:hover { background: #fff6e0; }
    .kt-ikon.merah { border-color: #f1c0c0; color: #c0392b; }
    .kt-ikon.merah:hover { background: #fdeeee; }
    .kt-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, .45); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 16px; }
    .kt-overlay[hidden] { display: none; }
    .kt-overlay-box { background: #fff; border-radius: 14px; padding: 18px 20px; width: 100%; max-width: 520px; max-height: 80vh; overflow: auto; }
    #ktPesertaZoomList li { padding: 4px 0; border-bottom: 1px solid #f0f0f0; }
</style>
<script>
    function muatPesertaZoom(btn, url) {
        if (btn.disabled) return;
        btn.disabled = true;
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.message || 'Gagal mengambil peserta Zoom.');
                var list = document.getElementById('ktPesertaZoomList');
                list.innerHTML = '';
                if (!res.d.length) {
                    var kosong = document.createElement('li');
                    kosong.className = 'text-muted';
                    kosong.textContent = 'Belum ada peserta yang terdaftar di Zoom.';
                    list.appendChild(kosong);
                }
                res.d.forEach(function (p) {
                    var li = document.createElement('li');
                    li.textContent = ((p.first_name || '') + ' ' + (p.last_name || '')).trim() + ' (' + (p.email || '-') + ')';
                    list.appendChild(li);
                });
                document.getElementById('ktPesertaZoom').hidden = false;
            })
            .catch(function (e) { alert(e.message || 'Gagal mengambil peserta Zoom.'); })
            .finally(function () { btn.disabled = false; });
    }
    function tutupPesertaZoom() { document.getElementById('ktPesertaZoom').hidden = true; }
    document.getElementById('ktPesertaZoom').addEventListener('click', function (e) { if (e.target === this) tutupPesertaZoom(); });
</script>
@endsection
