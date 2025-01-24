<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialOferta extends Model
{
    use HasFactory;

    // Definir las columnas que se pueden asignar masivamente
    protected $fillable = [
        'oferta_id', 'recolector_id', 'estado',
    ];

    // Relación con el modelo Oferta (la oferta que fue aceptada o rechazada)
    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    // Relación con el modelo User (el recolector que aceptó o rechazó la oferta)
    public function recolector()
    {
        return $this->belongsTo(User::class, 'recolector_id');
    }
}
