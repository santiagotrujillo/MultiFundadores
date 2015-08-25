<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = ['id' ,'tipo_pago_id','propiedad_id'];

    protected $table = 'pagos';


    // relaciones

    public function tipo_pago()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_pago','tipo_pago_id');
    }

    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad','propiedad_id');
    }

}