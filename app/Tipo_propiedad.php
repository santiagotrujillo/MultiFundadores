<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_Propiedad extends Model
{
    protected $fillable = ['id' ,'nombre','descripcion'];

    protected $table = 'tipo_propiedades';

    // relaciones

    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad');
    }
}