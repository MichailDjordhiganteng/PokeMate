<div data-duration-picker>
    <select id="{{ $inputId ?? 'duration' }}" class="duration-select" name="duration" required data-required-message="Pilih durasi sewa antara 1 sampai 5 hari.">@for($day = 1; $day <= 5; $day++)<option value="{{ $day }}" @selected((int) ($selectedDuration ?? 3) === $day)>{{ $day }} Hari</option>@endfor</select>
    <div class="duration-options" role="group" aria-label="Pilih durasi sewa">@foreach([1 => 'Kilat', 2 => 'Akhir Pekan', 3 => 'Ekspedisi', 4 => 'Jelajah', 5 => 'Maksimal'] as $day => $label)<button type="button" data-duration="{{ $day }}" data-duration-target="{{ $inputId ?? 'duration' }}" aria-pressed="false"><strong>{{ $day }} Hari</strong><small>{{ $label }}</small></button>@endforeach</div>
</div>
