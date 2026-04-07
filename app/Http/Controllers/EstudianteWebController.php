<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Tutor;
use Illuminate\Http\Request;

class EstudianteWebController extends Controller
{
    public function index()
    {
        $estudiantes = Estudiante::with('tutor')->get();
        return view('estudiantes', compact('estudiantes'));
    }

    public function create()
    {
        $tutores = Tutor::all();
        return view('registrar_estudiante', compact('tutores'));
    }

    // --- FUNCIÓN PARA GUARDAR (POST) ---
    public function store(Request $request)
    {
        // Validamos todos los campos de tu tabla MariaDB
        $validated = $request->validate([
            'nombre' => 'required|max:50',
            'apellido_p' => 'required|max:50',
            'apellido_m' => 'nullable|max:50',
            'curp' => 'nullable|unique:Estudiante,curp|max:18',
            'email' => 'nullable|email|unique:Estudiante,email',
            'sexo' => 'nullable|in:Mujer,Hombre,Otro',
            'fecha_nac' => 'nullable|date',
            'no_telefono' => 'nullable|max:15',
            'id_tutor' => 'required|exists:Tutor,id_tutor'
        ]);

        // Creamos el registro
        Estudiante::create($request->all());

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado con éxito');
    }

    // --- FUNCIÓN PARA MOSTRAR FORMULARIO DE EDICIÓN ---
    public function edit($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $tutores = Tutor::all();
        return view('editar_estudiante', compact('estudiante', 'tutores'));
    }

    // --- FUNCIÓN PARA ACTUALIZAR (PUT/PATCH) ---
    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required',
            'apellido_p' => 'required',
            'id_tutor' => 'required|exists:Tutor,id_tutor'
        ]);

        $estudiante->update($request->all());

        return redirect()->route('estudiantes.index')->with('success', 'Datos actualizados');
    }

    public function destroy($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $estudiante->delete();
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado');
    }
}