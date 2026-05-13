

<div class="mb-3">
    <label>Modul</label>
    <select name="kom_moduls_id" class="form-control">
        @foreach($moduls as $modul)
            <option value="{{ $modul->id }}" {{ old('kom_moduls_id', $zoom->kom_moduls_id ?? '') == $modul->id ? 'selected' : '' }}>
                {{ $modul->title }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Kelas</label>
    <select name="kom_class_id" class="form-control">
        @foreach($kelas as $k)
            <option value="{{ $k->id }}" {{ old('kom_class_id', $zoom->kom_class_id ?? '') == $k->id ? 'selected' : '' }}>
                {{ $k->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Jadwal</label>
    <select name="kom_schedules_id" class="form-control">
        @foreach($schedules as $schedule)
            <option value="{{ $schedule->id }}" {{ old('kom_schedules_id', $zoom->kom_schedules_id ?? '') == $schedule->id ? 'selected' : '' }}>
                {{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}
            </option>
        @endforeach
    </select>
</div>



<div class="mb-3">
    <label>Link Zoom</label>
    <textarea name="description" class="form-control">{{ old('description', $zoom->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif" {{ old('status', $zoom->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="nonaktif" {{ old('status', $zoom->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">{{ $button }}</button>
<a href="{{ route('kom.zoom.index') }}" class="btn btn-secondary">Kembali</a>
