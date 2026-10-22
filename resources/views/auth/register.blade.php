<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PokePartner</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f2f5fc;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .card {
            background: white;
            width: 420px;
            max-width: 100%;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(20,40,80,.10);
        }

        h1 {
            text-align: center;
            color: #142850;
        }

        p {
            text-align: center;
            color: #666;
        }

        label {
            display: block;
            font-weight: bold;
            color: #142850;
            margin: 14px 0 7px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d5dae5;
            border-radius: 9px;
            font: inherit;
        }

        textarea { resize: vertical; }

        button {
            background: #ffcb05;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 10px;
            font-weight: bold;
            color: #142850;
            cursor: pointer;
            margin-top: 22px;
        }

        a { color: #142850; }

        .error {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚡ Join PokePartner</h1>
        <p>Start your journey as a Trainer!</p>

        <form method="POST" action="{{ route('register.process') }}">
            @csrf

            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name"
                   value="{{ old('full_name') }}" required>
            @error('full_name') <div class="error">{{ $message }}</div> @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="phone">Phone Number</label>
            <input id="phone" name="phone"
                   value="{{ old('phone') }}" required>
            @error('phone') <div class="error">{{ $message }}</div> @enderror

            <label for="address">Address</label>
            <textarea id="address" name="address"
                      rows="3" required>{{ old('address') }}</textarea>
            @error('address') <div class="error">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input id="password" type="password"
                   name="password" minlength="8" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password"
                   name="password_confirmation" minlength="8" required>

            <button type="submit">Create Account</button>
        </form>

        <p>
            Already have an account?
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</body>
</html>
