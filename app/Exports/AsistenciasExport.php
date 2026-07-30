<?php

namespace App\Exports;

use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AsistenciasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sesionId;

    public function __construct(int $sesionId)
    {
        $this->sesionId = $sesionId;
    }

    public function collection()
    {
        return Asistencia::where('sesion_id', $this->sesionId)
            ->with('persona.usuario')
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Persona ID',
            'Nombres',
            'Apellidos',
            'Email',
            'Metodo',
            'Fecha Hora',
            'Registrado Por',
            'Minutes Attended'
        ];
    }

    public function map($row): array
    {
        $persona = $row->persona;
        return [
            $persona->id ?? null,
            $persona->nombres ?? null,
            $persona->apellidos ?? null,
            optional($persona->usuario)->email ?? null,
            $row->metodo,
            $row->fecha_hora ? Carbon::parse($row->fecha_hora)->toDateTimeString() : null,
            $row->registrado_por,
            $row->minutes_attended,
        ];
    }
}
