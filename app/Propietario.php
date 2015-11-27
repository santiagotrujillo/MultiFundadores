<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use SoftDeletes;

    protected $fillable = ['id' ,'nombre','apellido','telefono','clave'];

    protected $table = 'propietarios';

    protected $hidden = ['clave'];

    // relaciones

    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad');
    }
}