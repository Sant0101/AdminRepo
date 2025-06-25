<!-- filepath: resources/views/procedimientos.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procedimientos PostgreSQL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-xl mx-auto mt-10 bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Ejecutar Procedimiento Anónimo PL/pgSQL</h2>
        </div>
        <form method="POST" action="{{ route('ejecutar.procedimiento') }}" class="mb-6">
            @csrf
            <label class="block mb-2 font-semibold text-gray-700">Selecciona un procedimiento anónimo:</label>
            <select name="procedimiento" required class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Selecciona --</option>
                @foreach($procedimientos as $key => $label)
                    <option value="{{ $key }}" {{ old('procedimiento') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
                Ejecutar
            </button>
        </form>

        @isset($codigo)
            @if($codigo)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-1">Código ejecutado:</h3>
                    <pre class="bg-gray-100 border rounded p-3 text-sm overflow-x-auto">{{ $codigo }}</pre>
                </div>
            @endif
        @endisset

        @isset($resultado)
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Salida:</h3>
                <div class="bg-gray-50 border rounded p-3 text-sm overflow-x-auto">
                {{-- Ejercicio 1 y 5: Mostrar como tabla --}}
                @if(is_array($resultado) && isset($resultado['CODIGO']) && isset($resultado['PERSONA']))
                    <table class="min-w-full border border-gray-300 rounded mb-2">
                        <tr class="bg-gray-200">
                            @foreach(array_keys($resultado) as $col)
                                <th class="px-3 py-1">{{ $col }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($resultado as $val)
                                <td class="px-3 py-1">{{ $val }}</td>
                            @endforeach
                        </tr>
                    </table>
                {{-- Ejercicio 2: Arreglo de enteros --}}
                @elseif(is_array($resultado) && isset($resultado[0]) && is_int($resultado[0]))
                    <ul class="list-disc pl-6">
                        @foreach($resultado as $i => $val)
                            <li>Elemento {{ $i+1 }}: {{ $val }}</li>
                        @endforeach
                    </ul>
                {{-- Ejercicio 4: Mostrar tabla de personas --}}
                @elseif(is_array($resultado) && isset($resultado[0]->codigo))
                    <table class="min-w-full border border-gray-300 rounded mb-2">
                        <tr class="bg-gray-200">
                            @foreach(array_keys(get_object_vars($resultado[0])) as $col)
                                <th class="px-3 py-1">{{ $col }}</th>
                            @endforeach
                        </tr>
                        @foreach($resultado as $row)
                            <tr>
                                @foreach(get_object_vars($row) as $val)
                                    <td class="px-3 py-1">
                                        @if(is_array($val))
                                            {{ implode(', ', $val) }}
                                        @else
                                            {{ $val }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                {{-- Ejercicio 6: Mostrar tiempo de contrato --}}
                @elseif(is_array($resultado) && isset($resultado['AÑOS']))
                    <table class="min-w-full border border-gray-300 rounded mb-2">
                        <tr class="bg-gray-200">
                            <th class="px-3 py-1">Código</th>
                            <th class="px-3 py-1">Nombre</th>
                            <th class="px-3 py-1">Fecha de Contrato</th>
                            <th class="px-3 py-1">Años</th>
                            <th class="px-3 py-1">Meses</th>
                            <th class="px-3 py-1">Días</th>
                        </tr>
                        <tr>
                            <td class="px-3 py-1">{{ $resultado['CODIGO'] }}</td>
                            <td class="px-3 py-1">{{ $resultado['NOMBRE'] }}</td>
                            <td class="px-3 py-1">{{ $resultado['FECHA_CONTRATO'] }}</td>
                            <td class="px-3 py-1">{{ $resultado['AÑOS'] }}</td>
                            <td class="px-3 py-1">{{ $resultado['MESES'] }}</td>
                            <td class="px-3 py-1">{{ $resultado['DIAS'] }}</td>
                        </tr>
                    </table>
                @else
                    <pre>{{ is_string($resultado) ? $resultado : json_encode($resultado, JSON_PRETTY_PRINT) }}</pre>
                @endif
                </div>
            </div>
        @endisset
    </div>
</body>
</html>