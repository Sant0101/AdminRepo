<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UsuarioOracle;
use Illuminate\Support\Facades\DB;

class AuthOracleController extends Controller
{
    public function showLoginForm()
    {
        return view('oracle_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $username = $request->input('username');
        $password = $request->input('password');

        if (UsuarioOracle::validarCredenciales($username, $password)) {
            Session::put('oracle_user', $username);
            Session::put('oracle_pass', $password);
            return redirect()->route('oracle.dashboard');
        } else {
            return back()->withErrors(['login' => 'Credenciales inválidas']);
        }
    }

    public function logout()
    {
        Session::forget('oracle_user');
        Session::forget('oracle_pass');
        return redirect()->route('oracle.login');
    }

    public function dashboard()
    {
        $username = 'usuario1'; // Usuario fijo, sin login

        $tablas = \App\Models\UsuarioOracle::obtenerTablasYPrivilegios($username);
        $dbaSysPrivs = \App\Models\UsuarioOracle::obtenerDBASysPrivs($username);
        $dbaRolePrivs = \App\Models\UsuarioOracle::obtenerDBARolePrivs($username);

        return view('oracle_dashboard', compact(
            'username', 'tablas', 'dbaSysPrivs', 'dbaRolePrivs'
        ));
    }

    public function procedimientos()
    {
        // Lista de procedimientos disponibles
        $procedimientos = [
            'ejercicio1' => 'Ejercicio 1: RECORD simple',
            'ejercicio2' => 'Ejercicio 2: Arreglo de enteros',
            'ejercicio4' => 'Ejercicio 4: Tabla y arreglo',
            'ejercicio5' => 'Ejercicio 5: RECORD con fecha',
            'ejercicio6' => 'Ejercicio 6: Calcular tiempo de contrato',
        ];
        return view('procedimientos', compact('procedimientos'));
    }

    public function ejecutarProcedimiento(Request $request)
    {
        $procedimiento = $request->input('procedimiento');
        $resultado = null;
        $codigo = '';

        switch ($procedimiento) {
            case 'ejercicio1':
                $codigo = <<<EOT
DO \$\$
DECLARE
    -- Se define el tipo de registro con campos específicos
    V_VAR1 RECORD;
BEGIN
    -- Asignación de valores a los campos del registro V_VAR1
    V_VAR1 := ROW(1::INT, 'FRANCISCO'::TEXT, 30::INT);

    -- Impresión de los valores del registro en una sola línea
    RAISE NOTICE 'CODIGO: %, PERSONA: %, EDAD: %', V_VAR1.f1, V_VAR1.f2, V_VAR1.f3;
END \$\$;
EOT;
                $resultado = [
                    'CODIGO' => 1,
                    'PERSONA' => 'FRANCISCO',
                    'EDAD' => 30,
                ];
                break;

            case 'ejercicio2':
                $codigo = <<<EOT
DO $$
DECLARE
    -- Se declara un arreglo para almacenar números enteros
    V_LISTA INT[] := ARRAY[0, 0, 0, 0, 0, 0, 0, 0, 0, 0];  -- Arreglo con 10 posiciones (inicializado en cero)
    I INT;  -- Variable para el índice
BEGIN
    -- Se llena el arreglo con los números del 1 al 10
    FOR I IN 1..10 LOOP
        -- Asignación del valor I en la posición I-1 del arreglo (ya que los índices de los arreglos en PostgreSQL empiezan desde 1)
        V_LISTA[I-1] := I;

        -- Imprime el valor insertado en la consola
        RAISE NOTICE 'ELEMENTO EN LA POSICIÓN %: %', I, V_LISTA[I-1];
    END LOOP;
END $$;
EOT;
                $lista = [];
                for ($i = 1; $i <= 10; $i++) {
                    $lista[] = $i;
                }
                $resultado = $lista;
                break;

            case 'ejercicio4':
                $codigo = <<<EOT
-- Crea la tabla PERSONA con un campo de tipo arreglo
CREATE TABLE PERSONA (
    CODIGO INT,
    NOMBRE VARCHAR(25),
    LISTA TEXT[]  -- Tipo arreglo para almacenar los teléfonos
);

-- Inserta un registro en la tabla PERSONA con dos teléfonos
INSERT INTO PERSONA (CODIGO, NOMBRE, LISTA)
VALUES (
    1,
    'FRANCISCO',
    ARRAY['1234567', '7654321']  -- Inserta un arreglo con los números de teléfono
);

-- Confirma los cambios en la base de datos
COMMIT;

-- Consulta todos los registros de la tabla PERSONA
SELECT * FROM PERSONA;
EOT;
                DB::unprepared("
                    CREATE TABLE IF NOT EXISTS PERSONA (
                        CODIGO INT,
                        NOMBRE VARCHAR(25),
                        LISTA TEXT[]
                    );
                ");
                $existe = DB::table('persona')->where('codigo', 1)->exists();
                if (!$existe) {
                    DB::table('persona')->insert([
                        'codigo' => 1,
                        'nombre' => 'FRANCISCO',
                        'lista' => ['1234567', '7654321'],
                    ]);
                }
                $resultado = DB::select("SELECT * FROM persona");
                break;

            case 'ejercicio5':
                $codigo = <<<EOT
DO $$
DECLARE
    -- Se declara un tipo RECORD sin especificar un tipo complejo
    V_VAR1 RECORD;
BEGIN
    -- Asignación de valores a los campos del registro V_VAR1
    V_VAR1 := ROW(1, 'FRANCISCO', NULL, TO_DATE('11-05-2002', 'DD-MM-YYYY'));  -- CODIGO, NOMBRE, EDAD (null), FECHA_NACIMIENTO

    -- Cálculo de la edad a partir de la fecha de nacimiento
    V_VAR1.f3 := FLOOR(EXTRACT(YEAR FROM AGE(V_VAR1.f4)));  -- f4 corresponde a FECHA_NACIMIENTO y f3 a EDAD

    -- Impresión de los valores del registro en una sola línea con conversión explícita a texto
    RAISE NOTICE 'CODIGO: %, PERSONA: %, FECHA DE NACIMIENTO: %, EDAD: %',
        CAST(V_VAR1.f1 AS TEXT), CAST(V_VAR1.f2 AS TEXT), TO_CHAR(V_VAR1.f4, 'DD-MM-YYYY'), CAST(V_VAR1.f3 AS TEXT);
END $$;
EOT;
                $fecha_nacimiento = \DateTime::createFromFormat('d-m-Y', '11-05-2002');
                $hoy = new \DateTime();
                $edad = $hoy->diff($fecha_nacimiento)->y;
                $resultado = [
                    'CODIGO' => 1,
                    'PERSONA' => 'FRANCISCO',
                    'FECHA_NACIMIENTO' => $fecha_nacimiento->format('d-m-Y'),
                    'EDAD' => $edad,
                ];
                break;

            case 'ejercicio6':
                $codigo = <<<EOT
DO $$
DECLARE
    -- Declarar una variable de tipo RECORD con los campos de la tabla PERSONA
    V_VAR1 RECORD;
    
    -- Variables para almacenar los años, meses y días
    V_ANIOS INT;
    V_MESES INT;
    V_DIAS INT;
BEGIN
    -- Obtener el registro de la tabla PERSONA con CODIGO = 100
    SELECT CODIGO, NOMBRE, FECHA_CONTRATO
    INTO V_VAR1
    FROM PERSONAS
    WHERE CODIGO = 100;

    -- Calcular los años, meses y días transcurridos desde la fecha de contrato
    V_ANIOS := EXTRACT(YEAR FROM AGE(V_VAR1.FECHA_CONTRATO));
    V_MESES := EXTRACT(MONTH FROM AGE(V_VAR1.FECHA_CONTRATO));
    V_DIAS := EXTRACT(DAY FROM AGE(V_VAR1.FECHA_CONTRATO));

    -- Mostrar los resultados usando RAISE NOTICE
    RAISE NOTICE 'CODIGO: %, NOMBRE: %, FECHA DE CONTRATO: %, TIEMPO CONTRATO: % años, % meses, % días.',
        V_VAR1.CODIGO, V_VAR1.NOMBRE, TO_CHAR(V_VAR1.FECHA_CONTRATO, 'DD-MM-YYYY'),
        V_ANIOS, V_MESES, V_DIAS;
END $$;
EOT;
                $persona = DB::selectOne("SELECT CODIGO, NOMBRE, FECHA_CONTRATO FROM PERSONAS WHERE CODIGO = 100");
                if ($persona) {
                    $fechaContrato = new \DateTime($persona->fecha_contrato);
                    $hoy = new \DateTime();
                    $interval = $hoy->diff($fechaContrato);
                    $resultado = [
                        'CODIGO' => $persona->codigo,
                        'NOMBRE' => $persona->nombre,
                        'FECHA_CONTRATO' => $fechaContrato->format('d-m-Y'),
                        'AÑOS' => $interval->y,
                        'MESES' => $interval->m,
                        'DIAS' => $interval->d,
                    ];
                } else {
                    $resultado = 'No se encontró la persona con código 100.';
                }
                break;

            default:
                $resultado = 'Procedimiento no válido.';
                $codigo = '';
        }

        $procedimientos = [
            'ejercicio1' => 'Ejercicio 1: RECORD simple',
            'ejercicio2' => 'Ejercicio 2: Arreglo de enteros',
            'ejercicio4' => 'Ejercicio 4: Tabla y arreglo',
            'ejercicio5' => 'Ejercicio 5: RECORD con fecha',
            'ejercicio6' => 'Ejercicio 6: Calcular tiempo de contrato',
        ];
        return view('procedimientos', compact('procedimientos', 'resultado', 'codigo'));
    }
}