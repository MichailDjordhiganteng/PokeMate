
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pokémon - PokePartner</title>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            color: #142850;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 14px;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 18px 0 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font: inherit;
        }

        .types {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .types label {
            font-weight: normal;
            margin: 0;
        }

        .types input {
            width: auto;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        button {
            background: #ffcb05;
            color: #142850;
            border: none;
            border-radius: 8px;
            padding: 13px 20px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 24px;
        }

        a { color: #142850; }
    </style>
</head>
<body>

<div class="container">
    <h1>✏️ Edit Pokémon</h1>
    <p>Perbarui informasi {{ $pokemon->pokemon_name }}.</p>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.pokemons.update', $pokemon) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Kode Pokémon</label>
        <input type="text" name="pokemon_code"
               value="{{ old('pokemon_code', $pokemon->pokemon_code) }}"
               maxlength="30" required>

        <label>Nama Pokémon</label>
        <input type="text" name="pokemon_name"
               value="{{ old('pokemon_name', $pokemon->pokemon_name) }}"
               required>

        <label>Level</label>
        <input type="number" name="pokemon_level"
               value="{{ old('pokemon_level', $pokemon->pokemon_level) }}"
               min="1" max="100" required>

        <label>Deskripsi</label>
        <textarea name="description" rows="4"
                  required>{{ old('description', $pokemon->description) }}</textarea>

        <label>Harga Sewa (Rp)</label>
        <input type="number" name="rental_price"
               value="{{ old('rental_price', $pokemon->rental_price) }}"
               min="0" step="0.01" required>

        <label>Status Pokémon</label>
        <select name="status" required>
            @foreach ([
                'available' => 'Available',
                'resting' => 'Resting',
                'unavailable' => 'Unavailable'
            ] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('status', $pokemon->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <label>Tipe Pokémon</label>
        @php
            $selectedTypes = old(
                'type_ids',
                $pokemon->types->pluck('id')->all()
            );
        @endphp

        <div class="types">
            @foreach ($types as $type)
                <label>
                    <input type="checkbox"
                           name="type_ids[]"
                           value="{{ $type->id }}"
                           @checked(in_array($type->id, $selectedTypes))>
                    {{ $type->type }}
                </label>
            @endforeach
        </div>

        <label>Ganti Gambar Pokémon (Opsional)</label>

        @if ($pokemon->pokemon_image)
            <p>
                <img src="{{ asset('storage/' . $pokemon->pokemon_image) }}"
                     alt="{{ $pokemon->pokemon_name }}"
                     style="max-width:150px;">
            </p>
        @endif

        <input type="file" name="pokemon_image"
               accept=".jpg,.jpeg,.png,.webp">

        <div>
            <button type="submit">Simpan Perubahan</button>
        </div>
    </form>

    <p>
        <a href="{{ route('admin.pokemons.index') }}">
            ← Kembali ke Daftar Pokémon
        </a>
    </p>
</div>

</body>
</html>
