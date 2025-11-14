<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    protected $table = 'niveles';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'modulo_id',
        'nombre',
        'descripcion',
        'objetivos',
        'rango_edad',
        'rango_grado',
        'duracion_estimada_horas',
        'orden',
        'prerequisito_nivel_id',
        'created_at',
        'updated_at'
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id', 'id');
    }

    public function temas()
    {
        return $this->hasMany(Tema::class, 'nivel_id', 'id')
            ->orderBy('orden', 'asc');
    }

    public function prerequisito()
    {
        return $this->belongsTo(Nivel::class, 'prerequisito_nivel_id', 'id');
    }
}
