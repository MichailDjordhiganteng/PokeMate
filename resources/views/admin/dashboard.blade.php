<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PokePartner</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            color: #142850;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
        }

        .menu {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 0;
        }

        .menu a {
            display: inline-block;
            padding: 15px 20px;
            background: #ffcb05;
            color: #142850;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>💙 Dashboard Admin PokePartner</h1>

    <p>Welcome, {{ auth()->user()->full_name }}!</p>
    <p>Role: {{ auth()->user()->role }}</p>

    <h2>Menu Admin</h2>

    <div class="menu">
        <a href="{{ route('admin.pokemons.index') }}">
            ⚡ Kelola Pokémon
        </a>

        <a href="{{ route('admin.pokemons.create') }}">
            + Tambah Pokémon
        </a>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</div>

</body>
</html>
