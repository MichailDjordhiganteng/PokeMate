<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pengaturan Rental - PokePartner</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 20px;
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            color: #243047;
        }

        .container {
            max-width: 680px;
            margin: 0 auto;
            padding: 32px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .back-link {
            color: #172d50;
            text-decoration: none;
            font-size: 14px;
        }

        h1 {
            color: #172d50;
            margin-top: 24px;
            margin-bottom: 10px;
        }

        .description {
            color: #687386;
            line-height: 1.6;
        }

        .current-value {
            margin: 24px 0;
            padding: 20px;
            background: #f0f4fa;
            border-left: 5px solid #efbe31;
            border-radius: 8px;
        }

        .current-value strong {
            display: block;
            font-size: 28px;
            margin-top: 8px;
            color: #172d50;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd3df;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background: #172d50;
            color: #ffffff;
            padding: 13px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 16px;
        }

        button:hover {
            background: #254572;
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .success {
            background: #e7f7ec;
            color: #176b36;
        }

        .error {
            background: #fdecec;
            color: #b42318;
        }

        .hint {
            color: #687386;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="container">

        <a href="{{ route('admin.dashboard') }}" class="back-link">
            &larr; Kembali ke Dashboard Admin
        </a>

        <h1>Pengaturan Rental</h1>

        <p class="description">
            Atur jumlah maksimal Pokémon yang dapat disewa oleh
            setiap Trainer dalam transaksi yang belum selesai.
        </p>

        @if (session('success'))
            <div class="alert success">
                {{ session('success') }}
            </div>
        @endif

        <div class="current-value">
            Batas rental saat ini

            <strong>
                {{ $setting->max_active_pokemon }} Pokémon
            </strong>
        </div>

        <form
            action="{{ route('admin.rental-settings.update') }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <label for="max_active_pokemon">
                Maksimal Pokémon per Trainer
            </label>

            <input
                type="number"
                id="max_active_pokemon"
                name="max_active_pokemon"
                min="1"
                max="20"
                step="1"
                required
                value="{{ old('max_active_pokemon', $setting->max_active_pokemon) }}"
            >

            @error('max_active_pokemon')
                <div class="alert error">
                    {{ $message }}
                </div>
            @enderror

            <p class="hint">
                Nilai yang diperbolehkan adalah 1 sampai 20 Pokémon.
                Perubahan kuota berlaku untuk pengajuan rental berikutnya.
                Rental yang sedang berlangsung tidak otomatis dibatalkan.
            </p>

            <button type="submit">
                Simpan Pengaturan
            </button>
        </form>

    </div>
</body>
</html>
