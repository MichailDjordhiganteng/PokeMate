<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Pokémon - PokePartner</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            color: #142850;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1150px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 14px;
        }

        h1 {
            margin-top: 0;
        }

        .add-button {
            display: inline-block;
            background: #ffcb05;
            color: #142850;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            margin: 10px 0;
        }

        .add-button:hover {
            background: #f1bf00;
        }

        .search-box {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            flex: 1;
            min-width: 220px;
        }

        button {
            background: #ffcb05;
            color: #142850;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #142850;
            color: white;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .available {
            background: #dcfce7;
            color: #166534;
        }

        .on_adventure {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .resting {
            background: #fef3c7;
            color: #92400e;
        }

        .unavailable {
            background: #fee2e2;
            color: #991b1b;
        }

        .pagination {
            margin-top: 24px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin: 16px 0;
        }

        /* Tombol Edit Pokémon */
        .edit-button {
            display: inline-block;
            padding: 8px 12px;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .edit-button:hover {
            background: #bfdbfe;
        }

        a {
            color: #142850;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>⚡ Kelola Pokémon</h1>

    <p>Daftar seluruh unit Pokémon di PokePartner.</p>

    <!-- Tombol Tambah Pokémon -->
    <p>
        <a href="{{ route('admin.pokemons.create') }}"
           class="add-button">
            + Tambah Pokémon Baru
        </a>
    </p>

    <!-- Pesan berhasil -->
    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian -->
    <form
        method="GET"
        action="{{ route('admin.pokemons.index') }}"
        class="search-box"
    >
        <input
            type="text"
            name="search"
            placeholder="Cari nama atau kode Pokémon..."
            value="{{ request('search') }}"
        >

        <button type="submit">Cari Pokémon</button>

        <a href="{{ route('admin.pokemons.index') }}">
            Reset
        </a>
    </form>

    <!-- Tabel Pokémon -->
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Pokémon</th>
                    <th>Tipe</th>
                    <th>Level</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pokemons as $pokemon)
                    <tr>
                        <td>{{ $pokemon->pokemon_code }}</td>

                        <td>
                            <strong>{{ $pokemon->pokemon_name }}</strong>
                        </td>

                        <td>
                            {{ $pokemon->types->pluck('type')->join(', ') ?: '-' }}
                        </td>

                        <td>
                            Lv. {{ $pokemon->pokemon_level }}
                        </td>

                        <td>
                            Rp{{ number_format($pokemon->rental_price, 0, ',', '.') }}
                        </td>

                        <td>
                            <span class="badge {{ $pokemon->status }}">
                                @switch($pokemon->status)
                                    @case('available')
                                        Tersedia
                                        @break

                                    @case('on_adventure')
                                        Sedang Disewa
                                        @break

                                    @case('resting')
                                        Istirahat
                                        @break

                                    @case('unavailable')
                                        Tidak Tersedia
                                        @break

                                    @default
                                        {{ $pokemon->status }}
                                @endswitch
                            </span>
                        </td>

                        <!-- Tombol Edit -->
                        <td>
                            <a
                                href="{{ route('admin.pokemons.edit', $pokemon) }}"
                                class="edit-button"
                            >
                                ✏️ Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">
                            Tidak ada Pokémon yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <p>
            Menampilkan {{ $pokemons->count() }}
            dari {{ $pokemons->total() }} Pokémon
        </p>

        @if ($pokemons->hasPages())
            @if (!$pokemons->onFirstPage())
                <a href="{{ $pokemons->previousPageUrl() }}">
                    ← Sebelumnya
                </a>
            @endif

            <span style="margin: 0 14px;">
                Halaman {{ $pokemons->currentPage() }}
                dari {{ $pokemons->lastPage() }}
            </span>

            @if ($pokemons->hasMorePages())
                <a href="{{ $pokemons->nextPageUrl() }}">
                    Berikutnya →
                </a>
            @endif
        @endif
    </div>

    <hr style="margin: 30px 0;">

    <a href="{{ route('admin.dashboard') }}">
        ← Kembali ke Dashboard Admin
    </a>

</div>

</body>
</html>
