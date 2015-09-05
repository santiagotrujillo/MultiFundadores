<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Usuario extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $fillable = ['id' ,'nombre','apellido','email','rol_id','clave', 'remember_token'];

    protected $table = 'usuarios';

    protected $hidden = ['clave'];

    // relaciones

    public function rol()
    {
        return $this->belongsTo(__NAMESPACE__.'\Rol','rol_id');
    }
}
