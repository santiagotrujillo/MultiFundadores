<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pago
 * @package App
 */
class Pago extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' , 'tipo_pago_id', 'propiedad_id', 'valor', 'descripcion', 'fecha_inicial', 'fecha_final', 'valor_pagado'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pagos';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['deuda'];


    /**
     * @return mixed
     */
    public function getDeudaAttribute()
    {
        return $this->attributes['valor']- $this->attributes['valor_pagado'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipo_pago()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_pago','tipo_pago_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad','propiedad_id');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function abonos()
    {
        return $this->belongsTo(__NAMESPACE__.'\Abono');
    }

}