<div class="split"><section>
<label for="pokemon_code">Kode Pokémon</label><input id="pokemon_code" name="pokemon_code" maxlength="30" value="{{ old('pokemon_code', $pokemon->pokemon_code ?? '') }}" required>
<label for="pokemon_name">Nama Pokémon</label><input id="pokemon_name" name="pokemon_name" maxlength="100" value="{{ old('pokemon_name', $pokemon->pokemon_name ?? '') }}" required>
<label for="pokemon_level">Level</label><input id="pokemon_level" type="number" name="pokemon_level" min="1" max="100" value="{{ old('pokemon_level', $pokemon->pokemon_level ?? 1) }}" required>
<label for="description">Deskripsi Partner</label><textarea id="description" name="description" rows="5" required>{{ old('description', $pokemon->description ?? '') }}</textarea>
</section><section>
<label for="rental_price">Tarif Sewa per Hari (Rp)</label><input id="rental_price" type="number" step="0.01" min="0" max="9999999999.99" name="rental_price" value="{{ old('rental_price', $pokemon->rental_price ?? '') }}" required>
<label for="status">Status</label><select id="status" name="status" @disabled(isset($pokemon) && $pokemon->status === 'on_adventure')>@if(isset($pokemon) && $pokemon->status === 'on_adventure')<option value="on_adventure" selected>{{ __('status.on_adventure') }}</option>@endif @foreach(['available', 'resting', 'unavailable'] as $status)<option value="{{ $status }}" @selected(old('status', $pokemon->status ?? 'available') === $status)>{{ __('status.'.$status) }}</option>@endforeach</select>
<label>Tipe Pokémon (Pilih Minimal Satu)</label>
<div class="type-picker">@foreach($types as $type)<label class="check"><input type="checkbox" name="type_ids[]" value="{{ $type->id }}" @checked(in_array($type->id, old('type_ids', isset($pokemon) ? $pokemon->types->modelKeys() : [])))><x-type-badge :name="$type->type" /></label>@endforeach</div>
<label for="pokemon_image">Gambar (JPG, PNG, WebP; Maksimal 2 MB)</label><x-file-upload id="pokemon_image" name="pokemon_image" />
@if(isset($pokemon) && $pokemon->pokemon_image)<p><img style="height:110px" src="{{ asset('storage/'.$pokemon->pokemon_image) }}" alt="{{ $pokemon->pokemon_name }}"></p>@endif
</section></div>
