<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModalidadController extends Controller
{
    public function index(): View
    {
        $modalidades = Modalidad::orderBy('id', 'desc')->get();
        return view('estructura_de_la_actividad.modalidad', compact('modalidades'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|string|in:active,inactive',
        ], [
            'nombre.required' => 'El nombre de la modalidad es obligatorio.',
            'nombre.max'      => 'El nombre no puede exceder los 100 caracteres.',
            'estado.in'       => 'El estado seleccionado no es válido.'
        ]);

        Modalidad::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado'      => $request->estado === 'active',
        ]);

        return redirect()->route('superuser.modalidad')
            ->with('success', 'Modalidad creada con éxito.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $modalidad = Modalidad::findOrFail($id);

        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|string|in:active,inactive',
        ], [
            'nombre.required' => 'El nombre de la modalidad es obligatorio.',
            'nombre.max'      => 'El nombre no puede exceder los 100 caracteres.',
            'estado.in'       => 'El estado seleccionado no es válido.'
        ]);

        $modalidad->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado'      => $request->estado === 'active',
        ]);

        return redirect()->route('superuser.modalidad')
            ->with('success', 'Modalidad actualizada con éxito.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $modalidad = Modalidad::findOrFail($id);

        $modalidad->update([
            'estado' => false
        ]);

        return redirect()->route('superuser.modalidad')
            ->with('success', 'Modalidad inhabilitada con éxito.');
    }
}