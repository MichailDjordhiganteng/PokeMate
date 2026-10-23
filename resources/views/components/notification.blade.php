@props(['type' => 'info', 'title' => null, 'message' => null, 'details' => [], 'duration' => null, 'dismissLabel' => null])
@php
    $variants = [
        'success' => ['title' => 'Berhasil!', 'ball' => 'friend-ball', 'duration' => 6500, 'label' => 'berhasil'],
        'error' => ['title' => 'Gagal!', 'ball' => 'poke-ball', 'duration' => 10000, 'label' => 'gagal'],
        'warning' => ['title' => 'Perhatian!', 'ball' => 'ultra-ball', 'duration' => 10000, 'label' => 'peringatan'],
        'info' => ['title' => 'Informasi', 'ball' => 'great-ball', 'duration' => 8000, 'label' => 'informasi'],
    ];
    $type = array_key_exists($type, $variants) ? $type : 'info';
    $variant = $variants[$type];
    $duration = $duration ?? $variant['duration'];
@endphp
<section class="feedback feedback-{{ $type }}" role="{{ in_array($type, ['error', 'warning']) ? 'alert' : 'status' }}"
         aria-atomic="true" data-notification data-duration="{{ $duration }}">
    <span class="feedback-icon" aria-hidden="true">
        <img src="{{ asset('images/pokeballs/'.$variant['ball'].'.svg') }}" alt="" width="52" height="52">
    </span>
    <div class="feedback-content">
        <strong>{{ $title ?? $variant['title'] }}</strong>
        @if($message)<p>{{ $message }}</p>@endif
        @if(count($details))<ul>@foreach($details as $detail)<li>{{ $detail }}</li>@endforeach</ul>@endif
    </div>
    <span class="feedback-watermark" aria-hidden="true"></span>
    <button class="feedback-close" type="button" data-dismiss-notification
            aria-label="{{ $dismissLabel ?? 'Tutup notifikasi '.$variant['label'] }}">×</button>
    <div class="feedback-progress" aria-hidden="true"><span data-notification-progress></span></div>
</section>
