<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoRecurso extends Model
{
    protected $table = 'contenido_recursos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'contenido_id',
        'recurso_id',
        'orden',
        'es_principal',
        'created_at'
    ];

    public function contenido()
    {
        return $this->belongsTo(Contenido::class, 'contenido_id');
    }

    public function recurso()
    {
        return $this->belongsTo(RecursoMultimedia::class, 'recurso_id');
    }
}
