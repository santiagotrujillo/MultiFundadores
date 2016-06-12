<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipo_pago
 * @package App
 */
class Tipo_pago extends Model
{
    /**
     * @var int
     */
    const ADMINISTRACION = 1;

    /**
     * @var int
     */
    const SEGURO = 2;

    /**
     * @var int
     */
    const SALON = 3;

    /**
     * @var int
     */
    const MULTA = 4;

    /**
     * @var int
     */
    const PARQUEADERO = 5;

    /**
     * @var int
     */
    const OTROS = 6;

    /**
     * @var array
     */
    protected $fillable = ['id' ,'concepto','pago_valor','fecha_inicial','fecha_final','descripcion'];

    /**
     * @var string
     */
    protected $table = 'tipo_pagos';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pago()
    {
        return $this->belongsTo(__NAMESPACE__.'\Pago');
    }
}