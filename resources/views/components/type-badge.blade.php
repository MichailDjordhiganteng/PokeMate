@props(['name'])
@php
    $types = ['Grass' => ['Rumput', 'leaf'], 'Fire' => ['Api', 'fire'], 'Water' => ['Air', 'water'], 'Electric' => ['Listrik', 'bolt'], 'Flying' => ['Terbang', 'compass'], 'Psychic' => ['Psikis', 'sparkle'], 'Poison' => ['Racun', 'types'], 'Fairy' => ['Peri', 'sparkle'], 'Ghost' => ['Hantu', 'moon'], 'Dark' => ['Gelap', 'moon'], 'Rock' => ['Batu', 'mountain'], 'Ground' => ['Tanah', 'mountain'], 'Ice' => ['Es', 'sparkle'], 'Bug' => ['Serangga', 'leaf'], 'Fighting' => ['Petarung', 'shield'], 'Steel' => ['Baja', 'shield'], 'Dragon' => ['Naga', 'fire'], 'Normal' => ['Normal', 'circle']];
    [$label, $icon] = $types[$name] ?? [$name, 'circle'];
@endphp
<span class="badge element element-{{ strtolower($name) }}" title="{{ $name }}"><x-icon :name="$icon" />{{ $label }}</span>
