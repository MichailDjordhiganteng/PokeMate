<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PokePartner</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f2f5fc;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 380px;
            max-width: calc(100% - 32px);
            padding: 36px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(20, 40, 80, 0.10);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #142850;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #142850;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 20px;
            border: 1px solid #d5dae5;
            border-radius: 10px;
            font-size: 14px;
        }

        input:focus {
            outline: 2px solid #ffcb05;
            border-color: transparent;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #ffcb05;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            color: #142850;
            cursor: pointer;
        }

        button:hover {
            background: #f1bf00;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin-top: 22px;
        }

        .footer a {
            color: #142850;
            font-weight: bold;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <div class="logo">⚡ PokePartner</div>

        <p class="subtitle">
            Welcome back, Trainer!
        </p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                placeholder="Enter your email"
                value="{{ old('email') }}"
                required
            >

            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">
                Login
            </button>
        </form>

        <p class="footer">
            Don't have an account?
            <a href="{{ route('register') }}">Register here</a>
        </p>

    </div>

</body>
</html>
