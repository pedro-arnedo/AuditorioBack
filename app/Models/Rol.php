<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'permisos',
    ];

    protected $casts = [
        'permisos' => 'array'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id', 'id');
    }
}
