<!-- resources/views/oracle_dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Oracle</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Bienvenido, {{ $username }}</h2>
            <form method="POST" action="{{ route('oracle.logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow">
                    Cerrar sesión
                </button>
            </form>
        </div>

        {{-- Tablas y Privilegios --}}
        <div class="mb-10">
            <h3 class="text-xl font-semibold mb-2">Tablas y privilegios</h3>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 rounded shadow bg-white">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2">Tabla</th>
                            <th class="px-4 py-2">Privilegio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tablas as $tabla)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $tabla->table_name }}</td>
                                <td class="px-4 py-2">{{ $tabla->privilege }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-center text-gray-500">No hay tablas o privilegios asignados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Privilegios DBA --}}
        <div class="mb-10">
            <h3 class="text-xl font-semibold mb-2">Privilegios DBA (dba_sys_privs)</h3>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 rounded shadow bg-white">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2">Privilegio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dbaSysPrivs as $priv)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $priv->privilege }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-2 text-center text-gray-500">No tiene privilegios DBA asignados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Roles DBA --}}
        <div class="mb-10">
            <h3 class="text-xl font-semibold mb-2">Roles DBA (dba_role_privs)</h3>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 rounded shadow bg-white">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2">Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dbaRolePrivs as $rol)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $rol->granted_role }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-2 text-center text-gray-500">No tiene roles DBA asignados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
