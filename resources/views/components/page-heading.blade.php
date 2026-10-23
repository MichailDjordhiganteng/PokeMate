@props(['title', 'eyebrow' => '', 'description' => ''])
<div {{ $attributes->class(['page-heading']) }}><div>@if($eyebrow)<span class="eyebrow heading-tag">{{ $eyebrow }}</span>@endif<h1>{{ $title }}</h1>@if($description)<p class="muted">{{ $description }}</p>@endif</div>@if(!$slot->isEmpty())<div class="actions">{{ $slot }}</div>@endif</div>
