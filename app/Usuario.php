<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $fillable = ['id' ,'nombre','apellido','email','rol_id','clave'];

    protected $table = 'usuarios';

    protected $hidden = ['clave'];

    // relaciones

    public function rol()
    {
        return $this->belongsTo(__NAMESPACE__.'\Rol','rol_id');
    }
}
