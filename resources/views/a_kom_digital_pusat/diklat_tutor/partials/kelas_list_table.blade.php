@if($kelasTutorials->isEmpty())
    <div class="alert alert-info text-center">Tidak ada kelas tutorial ditemukan.</div>
@else
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th><i class="fas fa-book me-1 text-primary"></i>&nbsp;&nbsp;Modul</th>
                <th><i class="fas fa-graduation-cap me-1 text-success"></i>&nbsp;&nbsp;Kelas</th>
                <th><i class="fas fa-map-marker-alt me-1 text-info"></i>&nbsp;&nbsp;Rayon</th>
                <th><i class="fas fa-calendar-alt me-1 text-warning"></i>&nbsp;&nbsp;Waktu Pelaksanaan Kelas</th>
                
                <th><i class="fas fa-user-alt me-1 text-primary"></i>&nbsp;&nbsp;Kuota Tersisa</th>
                
                <th>Join Kelas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelasTutorials as $index => $kelas)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $kelas->modul->name ?? '-' }}</td>
                    <td>{{ $kelas->kelas->name ?? '-' }}</td>
                    <td>{{ $kelas->rayon->name ?? '-' }}</td>
                   
                    <td> {{ \Carbon\Carbon::parse($kelas->start_time)->translatedFormat('l, d F Y H:i') }}</td>
                    
                    <td class="text-center">
                        @if($kelas->sisa_quota > 0)
                            <span class="badge bg-primary text-white">{{ $kelas->sisa_quota }}</span>
                        @else
                            <span class="badge bg-danger">Penuh</span>
                        @endif
                    </td>
                   
                    <td>
                        @if($kelas->sisa_quota > 0)
                            <form action="{{ route('kom.diklat_tutor.joinDiklat') }}" method="POST" class="joinClassForm">
                                @csrf
                                <input type="hidden" name="class_tutorial_id" value="{{ $kelas->id }}">
                                <input type="hidden" name="category_diklat" value="{{ request('category_diklat') }}">
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                    <i class="fas fa-sign-in-alt me-1"></i> Ikut Diklat
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Kuota kelas sudah penuh">
                                <i class="fas fa-times-circle me-1"></i> Kuota Penuh
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>    
@endif