<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Propietario extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    protected $fillable = ['id' ,'nombre','apellido','telefono','clave'];

    protected $table = 'propietarios';

    protected $hidden = ['clave'];

    // relaciones

    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad');
    }
}