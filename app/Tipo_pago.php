<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_pago extends Model
{
    protected $fillable = ['id' ,'concepto','pago_valor','fecha_inicial','fecha_final','descripcion'];

    protected $table = 'tipo_pagos';

    public function pago()
    {
        return $this->belongsTo(__NAMESPACE__.'\Pago');
    }
}