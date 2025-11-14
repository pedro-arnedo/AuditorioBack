<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'temas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nivel_id',
        'nombre',
        'slug',
        'descripcion',
        'objetivos_aprendizaje',
        'duracion_estimada_minutos',
        'orden',
        'es_obligatorio',
        'puntos_recompensa',
        'created_at',
        'updated_at'
    ];

    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id', 'id');
    }

    public function contenidos()
    {
        return $this->hasMany(Contenido::class, 'tema_id', 'id')
            ->orderBy('orden', 'asc');
    }
}
