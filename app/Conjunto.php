<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conjunto extends Model
{
    protected $fillable = ['id' ,'nombre','direccion','ciudad'];

    protected $table = 'conjuntos';

    // relaciones

    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad');
    }
}
