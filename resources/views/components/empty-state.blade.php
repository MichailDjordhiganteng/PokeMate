@props(['title' => 'Belum ada data', 'description' => 'Data akan ditampilkan di sini setelah tersedia.', 'icon' => 'pokemon'])
<div class="empty-state"><span><x-icon :name="$icon" /></span><h3>{{ $title }}</h3><p class="muted">{{ $description }}</p>{{ $slot }}</div>
