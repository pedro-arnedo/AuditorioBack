<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoMultimedia extends Model
{
    protected $table = 'recursos_multimedia';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'url_archivo',
        'ruta_local',
        'thumbnail_url',
        'tamanio_bytes',
        'duracion_segundos',
        'estado_procesamiento',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function contenidos()
    {
        return $this->belongsToMany(
            Contenido::class,
            'contenido_recursos',
            'recurso_id',
            'contenido_id'
        )
        ->withPivot(['orden', 'es_principal'])
        ->orderBy('orden');
    }
}
