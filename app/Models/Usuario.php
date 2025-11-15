<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'institucion_id',
        'foto_perfil',
        'estado',
        'motivo_suspension',
        'debe_cambiar_password',
        'fecha_ultimo_acceso',
        'email_verified_at',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'debe_cambiar_password' => 'boolean',
        'fecha_ultimo_acceso' => 'datetime',
        'email_verified_at' => 'datetime'
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id', 'id');
    }
}
