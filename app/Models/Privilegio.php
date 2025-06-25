<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    protected $table = 'PRIVILEGIOS'; // Nombre de tu tabla en Oracle
    protected $primaryKey = 'ID_PRIVILEGIO';

    protected $fillable = [
        'ID_USUARIO',
        'NOMBRE_TABLA',
        'PERMISO_SELECT',
        'PERMISO_INSERT',
        'PERMISO_UPDATE',
        'PERMISO_DELETE',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ID_USUARIO', 'ID_USUARIO');
    }
}