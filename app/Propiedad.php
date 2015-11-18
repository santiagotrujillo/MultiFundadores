<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    protected $fillable = ['id' ,'numero_pisos','tipo_propiedad_id','conjunto_id','propietario_id'];

    protected $table = 'propiedades';


    // relaciones

    public function tipo_propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_propiedad','tipo_propietdad_id');
    }

    public function propietario()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propietario','propietario_id');
    }

    public function conjunto()
    {
        return $this->belongsTo(__NAMESPACE__.'\Conjunto','conjunto_id');
    }
}