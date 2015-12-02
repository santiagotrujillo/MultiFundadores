<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deuda extends Model
{
    protected $fillable = ['id' ,'descripcion','conjunto_id','tipo_deuda_id','valor'];

    protected $table = 'deudas';

    // relaciones

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
