<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadUsuario extends Model
{
    protected $table = 'actividad_usuario';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'tipo_actividad',
        'referencia_tipo',
        'referencia_id',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
