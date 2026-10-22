{{-- kom_v02: titik kelas tutorial di TENGAH modul. Akhir modul selalu otomatis ada tutorial. --}}
@php
    $video = $video ?? null;
    $isTerakhir = false;
    if ($video && $video->modul_id) {
        $terakhir = \App\Models\Kmd\KmdVideo::where('modul_id', $video->modul_id)
            ->where('status', 'active')->orderByDesc('sort_video')->first();
        $isTerakhir = $terakhir && $terakhir->id === $video->id;
    }
@endphp
<div class="form-group">
    <div class="border rounded p-3" style="background:#f8fbff;">
        <div class="custom-control custom-checkbox">
            <input type="hidden" name="has_tutorial" value="0">
            <input type="checkbox" class="custom-control-input" id="has_tutorial" name="has_tutorial" value="1"
                {{ old('has_tutorial', optional($video)->has_tutorial) ? 'checked' : '' }}>
            <label class="custom-control-label font-weight-bold" for="has_tutorial">
                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i>{{ __('Ada Kelas Tutorial setelah video ini') }}
            </label>
        </div>
        <small class="text-muted d-block mt-2">
            {{ __('Centang jika peserta perlu mengikuti kelas tutorial di tengah modul, setelah menyelesaikan video ini.') }}
            {{ __('Di akhir setiap modul selalu ada kelas tutorial secara otomatis, jadi video terakhir tidak perlu dicentang.') }}
        </small>
        @if ($isTerakhir)
            <div class="small text-success mt-2">
                <i class="fas fa-info-circle mr-1"></i>{{ __('Ini video terakhir modul — kelas tutorial akhir modul sudah otomatis.') }}
            </div>
        @endif
    </div>
</div>
