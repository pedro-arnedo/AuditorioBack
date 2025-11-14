<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_unico',
        'imagen_portada',
        'color_tema',
        'estado',
        'institucion_id',
        'created_at',
        'updated_at'
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id', 'id');
    }

    public function niveles()
    {
        return $this->hasMany(Nivel::class, 'modulo_id', 'id')
            ->orderBy('orden', 'asc');
    }
}
