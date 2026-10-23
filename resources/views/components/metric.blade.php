@props(['label', 'value', 'hint' => '', 'icon' => 'pokemon', 'tone' => 'neutral', 'suffix' => ''])
<article {{ $attributes->class(['panel metric', 'tone-'.$tone]) }}><div><span class="eyebrow">{{ $label }}</span><strong>{{ $value }} @if($suffix)<small>{{ $suffix }}</small>@endif</strong>@if($hint)<p>{{ $hint }}</p>@endif</div><span class="metric-icon"><x-icon :name="$icon" /></span></article>
