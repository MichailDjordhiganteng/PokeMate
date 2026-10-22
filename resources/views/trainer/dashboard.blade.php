<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Trainer Dashboard - PokePartner</title>
</head>
<body>
    <h1>⚡ Welcome to PokePartner!</h1>

    <p>Hello, {{ auth()->user()->full_name }}!</p>
    <p>Role: {{ auth()->user()->role }}</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
