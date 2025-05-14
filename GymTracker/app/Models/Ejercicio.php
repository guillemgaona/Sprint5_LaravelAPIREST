<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = 'Ejercicio';
    protected $primaryKey = 'id_ejercicio';

    protected $fillable = [
        'nombre',
        'grupo_muscular',
        'descripcion',
        'imagen_demo',
    ];

    public function series()
    {
        return $this->hasMany(Serie::class, 'id_ejercicio', 'id_ejercicio');
    }

    public function getRouteKeyName()
    {
        return 'id_ejercicio';
    }
}