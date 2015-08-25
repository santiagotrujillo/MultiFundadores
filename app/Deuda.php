<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deuda extends Model
{
    protected $fillable = ['id' ,'fecha','descripcion','conjunto_id','tipo_deuda'];

    protected $table = 'deudas';

    // relaciones

    public function conjunto()
    {
        return $this->belongsTo(__NAMESPACE__.'\Conjunto','conjunto_id');
    }

    public function tipo_deuda()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_deuda','tipo_deuda');
    }
}
