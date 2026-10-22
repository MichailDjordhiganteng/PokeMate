<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pokémon - PokePartner</title>

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
            background: white;
            padding: 30px;
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

        textarea { resize: vertical; }

        .types {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 12px;
        }

        .types label {
            font-weight: normal;
            margin: 0;
        }

        .types input {
            width: auto;
        }

        .error {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 5px;
        }

        .button {
            background: #ffcb05;
            color: #142850;
            border: none;
            border-radius: 8px;
            padding: 13px 20px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 24px;
        }

        a { color: #142850; }
    </style>
</head>

<body>

<div class="container">

    <h1>⚡ Tambah Pokémon Baru</h1>

    <p>Isi data unit Pokémon yang ingin ditambahkan.</p>

    <form
        method="POST"
        action="{{ route('admin.pokemons.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <label for="pokemon_code">Kode Pokémon</label>
        <input
            id="pokemon_code"
            type="text"
            name="pokemon_code"
            value="{{ old('pokemon_code') }}"
            placeholder="Contoh: PKM009"
            maxlength="30"
            required
        >
        @error('pokemon_code')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="pokemon_name">Nama Pokémon</label>
        <input
            id="pokemon_name"
            type="text"
            name="pokemon_name"
            value="{{ old('pokemon_name') }}"
            placeholder="Contoh: Eevee"
            required
        >
        @error('pokemon_name')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="pokemon_level">Level</label>
        <input
            id="pokemon_level"
            type="number"
            name="pokemon_level"
            value="{{ old('pokemon_level', 1) }}"
            min="1"
            max="100"
            required
        >
        @error('pokemon_level')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="description">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            required
        >{{ old('description') }}</textarea>
        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="rental_price">Harga Sewa (Rp)</label>
        <input
            id="rental_price"
            type="number"
            name="rental_price"
            value="{{ old('rental_price') }}"
            min="0"
            step="0.01"
            required
        >
        @error('rental_price')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="status">Status Pokémon</label>
        <select id="status" name="status" required>
            <option value="available"
                @selected(old('status') === 'available')>
                Available
            </option>

            <option value="resting"
                @selected(old('status') === 'resting')>
                Resting
            </option>

            <option value="unavailable"
                @selected(old('status') === 'unavailable')>
                Unavailable
            </option>
        </select>
        @error('status')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Tipe Pokémon (boleh lebih dari satu)</label>

        <div class="types">
            @foreach ($types as $type)
                <label>
                    <input
                        type="checkbox"
                        name="type_ids[]"
                        value="{{ $type->id }}"
                        @checked(
                            in_array(
                                $type->id,
                                old('type_ids', [])
                            )
                        )
                    >
                    {{ $type->type }}
                </label>
            @endforeach
        </div>

        @error('type_ids')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('type_ids.*')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="pokemon_image">Gambar Pokémon</label>
        <input
            id="pokemon_image"
            type="file"
            name="pokemon_image"
            accept=".jpg,.jpeg,.png,.webp"
        >
        <small>Opsional. Maksimal 2 MB.</small>
        @error('pokemon_image')
            <div class="error">{{ $message }}</div>
        @enderror

        <div>
            <button type="submit" class="button">
                Simpan Pokémon
            </button>
        </div>
    </form>

    <p style="margin-top: 25px;">
        <a href="{{ route('admin.pokemons.index') }}">
            ← Kembali ke Daftar Pokémon
        </a>
    </p>

</div>

</body>
</html>
