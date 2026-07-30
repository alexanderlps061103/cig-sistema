<?php

namespace App\Exports;

use App\Models\Inscripcion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class InscritosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $actividadId;

    public function __construct(int $actividadId)
    {
        $this->actividadId = $actividadId;
    }

    public function collection()
    {
        return Inscripcion::where('actividad_id', $this->actividadId)
            ->with('persona.usuario')
            ->orderBy('fecha_inscripcion')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Persona ID',
            'Nombres',
            'Apellidos',
            'Email',
            'Fecha Inscripción',
            'Estado'
        ];
    }

    public function map($row): array
    {
        $p = $row->persona;
        return [
            $p->id ?? null,
            $p->nombres ?? null,
            $p->apellidos ?? null,
            optional($p->usuario)->email ?? null,
            $row->fecha_inscripcion ? Carbon::parse($row->fecha_inscripcion)->toDateTimeString() : null,
            $row->estado,
        ];
    }
}
