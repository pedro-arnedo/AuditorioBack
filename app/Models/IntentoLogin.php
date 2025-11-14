<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntentoLogin extends Model
{
    protected $table = 'intentos_login';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'email',
        'exitoso',
        'ip_address',
        'user_agent',
        'mensaje',
        'created_at'
    ];
}
