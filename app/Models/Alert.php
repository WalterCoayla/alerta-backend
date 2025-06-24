<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $table = 'alertas'; // <-- ¡Añade esta línea!

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'incident_type',
        'ubicacion'
        
        // Si usaste $table->timestamp('timestamp'); en la migración:
        // 'timestamp',
    ];
}
