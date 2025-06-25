<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
   public function index()
{
    $usuario = auth()->user();

    if (!$usuario) {
        // No hay usuario autenticado, redirige o muestra mensaje
        return redirect()->route('login'); 
        // o return view('home')->with('error', 'No autenticado');
    }

    $privilegios = $usuario->privilegios ?? collect();

    return view('home', compact('usuario', 'privilegios'));
}

}