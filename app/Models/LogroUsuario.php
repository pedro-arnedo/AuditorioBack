<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogroUsuario extends Model
{
    protected $table = 'logros_usuario';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'logro_id',
        'fecha_obtencion',
        'notificado',
        'created_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function logro()
    {
        return $this->belongsTo(Logro::class, 'logro_id');
    }
}
