<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    use HasFactory;

    protected $table = 'Sesion';
    protected $primaryKey = 'id_sesion';

    protected $fillable = [
        'user_id',
        'fecha',
        'nota',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function series()
    {
        return $this->hasMany(Serie::class, 'id_sesion', 'id_sesion');
    }

    public function getRouteKeyName()
    {
        return 'id_sesion';
    }
}