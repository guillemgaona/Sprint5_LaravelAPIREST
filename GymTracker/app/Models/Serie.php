<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    protected $table = 'Serie';
    protected $primaryKey = 'id_serie';

    protected $fillable = [
        'id_sesion',
        'id_ejercicio',
        'serie_num',
        'repeticiones',
        'peso',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
    ];

    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'id_sesion', 'id_sesion');
    }

    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class, 'id_ejercicio', 'id_ejercicio');
    }

    public function getRouteKeyName()
    {
        return 'id_serie';
    }
}