<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'ciudad',
        'pais',
        'telefono',
        'email_contacto',
        'logo',
        'configuracion',
        'estado'
    ];

    protected $casts = [
        'configuracion' => 'array'
    ];
}
