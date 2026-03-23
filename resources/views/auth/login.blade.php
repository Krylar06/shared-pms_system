<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Login</title>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <form method="POST" action="{{ route('login.submit') }}" class="bg-white p-6 rounded-lg shadow w-full max-w-sm">
        @csrf
        <h1 class="text-xl font-semibold mb-4">Admin Login</h1>

        <label class="block mb-2 text-sm">Email</label>
        <input name="email" type="email" value="{{ old('email') }}"
               class="w-full border rounded px-3 py-2 mb-3" required>

        <label class="block mb-2 text-sm">Password</label>
        <input name="password" type="password"
               class="w-full border rounded px-3 py-2 mb-3" required>

        @if ($errors->any())
            <div class="text-sm text-red-600 mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <button class="w-full bg-black text-white rounded px-3 py-2">
            Login
        </button>
    </form>
</body>
</html>