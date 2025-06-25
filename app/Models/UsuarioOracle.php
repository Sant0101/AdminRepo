<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UsuarioOracle
{
    public static function validarCredenciales($username, $password)
    {
        try {
            $config = config('database.connections.oracle');
            $config['username'] = $username;
            $config['password'] = $password;
            config(['database.connections.temp_oracle' => $config]);
            DB::purge('temp_oracle');
            DB::connection('temp_oracle')->getPdo();
            return true;
        } catch (\Exception $e) {
            // Devuelve el mensaje real para depuración
            throw $e;
            // return false;
        }
    }

    public static function obtenerTablasYPrivilegios($username)
    {
        $tablas = DB::connection('oracle')->select("
            SELECT table_name, privilege
            FROM user_tab_privs
            WHERE grantee = UPPER(?)
        ", [$username]);
        return $tablas;
    }

    public static function obtenerPrivilegiosSistema($username)
    {
        $privilegios = DB::connection('oracle')->select("
            SELECT privilege
            FROM user_sys_privs
            WHERE username = UPPER(?)
        ", [$username]);
        return $privilegios;
    }

    public static function obtenerRoles($username)
    {
        return DB::connection('oracle')->select("
            SELECT granted_role
            FROM user_role_privs
            WHERE username = UPPER(?)
        ", [$username]);
    }   

    public static function obtenerDBASysPrivs($username)
    {
        return DB::connection('oracle')->select("
            SELECT privilege
            FROM dba_sys_privs
            WHERE grantee = UPPER(?)
        ", [$username]);
    }

    public static function obtenerDBARolePrivs($username)
    {
        return DB::connection('oracle')->select("
            SELECT granted_role
            FROM dba_role_privs
            WHERE grantee = UPPER(?)
        ", [$username]);
    }
}
