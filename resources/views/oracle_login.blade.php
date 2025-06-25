<!-- resources/views/oracle_login.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Login Oracle</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Inicio de sesión Oracle</h2>

        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm text-center bg-red-100 border border-red-300 rounded p-2">
                {{ $errors->first('login') }}
            </div>
        @endif

        <form method="POST" action="{{ route('oracle.login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-gray-700 font-medium mb-1">Usuario</label>
                <input type="text" name="username" id="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
                    Ingresar
                </button>
            </div>
        </form>
    </div>

</body>
</html>
