<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Cancha extends Model
{
    protected $table = 'canchas';
    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'reservante_nombre',
        'reservante_apellidos',
        'hora_reserva',
        'fecha_reserva',
        'tiempo_reserva',
        'ticket',
    ];
    
    protected $connection = 'tenant'; // Asegúrate de usar la conexión 'tenant'

    public function canchasTipo()
    {
        return $this->belongsTo(CanchasTipo::class, 'nombre', 'nombre');
    }
}
