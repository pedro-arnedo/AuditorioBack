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
        'slug',
        'descripcion',
        'objetivos',
        'imagen_portada',
        'color_tema',
        'estado',
        'institucion_id',
        'creador_id',
        'fecha_publicacion',
        'metadata',
        'orden',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'fecha_publicacion' => 'datetime'
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }

    public function niveles()
    {
        return $this->hasMany(Nivel::class, 'modulo_id')->orderBy('orden');
    }
}
