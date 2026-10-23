@props(['pokemon', 'meta' => true])
<div {{ $attributes->class(['pokemon-art', 'type-'.strtolower($pokemon->types->first()?->type ?? 'normal')]) }}>
    @if($meta)<div class="art-meta"><span>{{ $pokemon->pokemon_code }}</span><span>Lv. {{ $pokemon->pokemon_level }}</span></div>@endif
    @if($pokemon->imageUrl())<img src="{{ $pokemon->imageUrl() }}" alt="{{ $pokemon->pokemon_name }}" loading="lazy">@else<img src="{{ asset('images/pokeball.png') }}" alt="{{ $pokemon->pokemon_name }}">@endif
    {{ $slot }}
</div>
