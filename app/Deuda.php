<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Deuda
 * @package App
 */
class Deuda extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'descripcion','conjunto_id','tipo_deuda_id','valor'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deudas';


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conjunto()
    {
        return $this->belongsTo(__NAMESPACE__.'\Conjunto','conjunto_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipo_deuda()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_deuda','tipo_deuda');
    }
}
