<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablePrivilege extends Model
{
    protected $fillable = [
        // columnas de la tabla privileges
    ];

    // Define la relación inversa si quieres
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
