<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipo_deuda
 * @package App
 */
class Tipo_deuda extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'concepto','valor','fecha_inicial','fecha_final','descripcion'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tipo_deudas';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deuda()
    {
        return $this->belongsTo(__NAMESPACE__.'\Deuda');
    }
}