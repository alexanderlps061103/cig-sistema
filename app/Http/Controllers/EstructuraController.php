<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EstructuraController extends Controller
{
    protected array $modelMap = [
        'modalidad'        => \App\Models\Modalidad::class,
        'denominacion'     => \App\Models\TipoActividad::class, 
        'salon'            => \App\Models\Salon::class,
        'firma'            => \App\Models\Firma::class,
        'sello'            => \App\Models\Sello::class,
        'feriado'          => \App\Models\Feriado::class, // Soporte añadido para feriados
    ];

    protected array $viewMap = [
        'modalidad'        => 'modalidades',
        'denominacion'     => 'denominaciones',
        'salon'            => 'salon',
        'firma'            => 'firma',     // Corregido a singular acorde a tu captura
        'sello'            => 'sello',     // Corregido a singular acorde a tu captura
        'feriado'          => 'feriados',  // Corregido a plural acorde a tu captura
    ];

    protected array $labels = [
        'modalidad'        => ['singular' => 'Modalidad', 'plural' => 'Modalidades'],
        'denominacion'     => ['singular' => 'Tipo de Actividad', 'plural' => 'Tipos de Actividad'],
        'salon'            => ['singular' => 'Salón', 'plural' => 'Salones'],
        'firma'            => ['singular' => 'Firma', 'plural' => 'Firmas'],
        'sello'            => ['singular' => 'Sello', 'plural' => 'Sellos'],
        'feriado'          => ['singular' => 'Feriado', 'plural' => 'Feriados'], // Soporte añadido
    ];

    private function normalizeModulo(string $modulo): string
    {
        $aliases = [
            'modalidades'     => 'modalidad',
            'tipos'           => 'denominacion',
            'denominaciones'  => 'denominacion',
            'salones'         => 'salon',
            'firmas'          => 'firma',
            'sellos'          => 'sello',
            'feriados'        => 'feriado', // Soporte añadido
        ];

        return $aliases[$modulo] ?? $modulo;
    }

    private function resolveModel(string $modulo)
    {
        if (!array_key_exists($modulo, $this->modelMap)) {
            abort(404, "El módulo '{$modulo}' no existe.");
        }
        return $this->modelMap[$modulo];
    }

    private function getValidationRules(string $modulo, $id = null): array
    {
        switch ($modulo) {
            case 'modalidad':
                return [
                    'nombre_modalidad' => 'required|string|max:255',
                ];
            case 'denominacion':
                return [
                    'nombre'      => 'required|string|max:255',
                    'descripcion' => 'nullable|string|max:1000',
                    'duracion'    => 'required',
                ];
            case 'salon':
                return [
                    'nombre'    => 'required|string|max:255',
                    'capacidad' => 'required|integer|min:1',
                ];
            case 'feriado':
                return [
                    'fecha'       => 'required|date',
                    'descripcion' => 'required|string|max:255',
                    'recurrente'  => 'nullable|boolean',
                ];
            case 'firma':
            case 'sello':
                return [
                    'nombre' => 'required|string|max:255',
                    'imagen' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                ];
            default:
                return [
                    'nombre'      => 'required|string|max:255',
                    'descripcion' => 'nullable|string|max:1000',
                ];
        }
    }

    public function index(Request $request, string $modulo)
    {
        $modulo = $this->normalizeModulo($modulo);
        $modelClass = $this->resolveModel($modulo);
        $labels = $this->labels[$modulo];
        
        $registros = $modelClass::all();
        $data = compact('registros', 'modulo', 'labels');

        $fileName = $this->viewMap[$modulo] ?? $modulo;
        $customView = "coordinador.entidades_crud.{$fileName}";

        if (view()->exists($customView)) {
            return view($customView, $data);
        }

        abort(500, "La vista no se encuentra en: resources/views/". str_replace('.', '/', $customView) . ".blade.php");
    }

    public function store(Request $request, string $modulo)
    {
        $modulo = $this->normalizeModulo($modulo);
        $modelClass = $this->resolveModel($modulo);
        
        $validatedData = $request->validate($this->getValidationRules($modulo));

        if ($request->has('estado')) {
            $validatedData['estado'] = ($request->estado === 'active' || $request->estado == '1');
        }

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('uploads/' . $modulo . 's', 'public');
            $validatedData['imagen'] = $path;
        }

        $modelClass::create($validatedData);

        return redirect()->back()->with('success', "{$this->labels[$modulo]['singular']} guardada correctamente.");
    }

    public function update(Request $request, string $modulo, $id)
    {
        $modulo = $this->normalizeModulo($modulo);
        $modelClass = $this->resolveModel($modulo);
        
        $validatedData = $request->validate($this->getValidationRules($modulo, $id));

        if ($request->has('estado')) {
            $validatedData['estado'] = ($request->estado === 'active' || $request->estado == '1');
        }

        $registro = $modelClass::findOrFail($id);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('uploads/' . $modulo . 's', 'public');
            $validatedData['imagen'] = $path;
        }

        $registro->update($validatedData);

        return redirect()->back()->with('success', "{$this->labels[$modulo]['singular']} actualizada correctamente.");
    }

    public function destroy(string $modulo, $id)
    {
        $modulo = $this->normalizeModulo($modulo);
        $modelClass = $this->resolveModel($modulo);
        $registro = $modelClass::findOrFail($id);
        
        $tableName = $registro->getTable();
        $accion = 'eliminada';

        if (Schema::hasColumn($tableName, 'estado')) {
            $registro->update(['estado' => false]);
            $accion = 'inhabilitada';
        } else {
            $registro->delete();
        }

        return redirect()->back()->with('success', "{$this->labels[$modulo]['singular']} {$accion} correctamente.");
    }
}