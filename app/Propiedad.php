<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Propiedad
 * @package App
 */
class Propiedad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'numero_pisos','tipo_propiedad_id','conjunto_id','propietario_id'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'propiedades';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipo_propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Tipo_propiedad','tipo_propiedad_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propietario()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propietario','propietario_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conjunto()
    {
        return $this->belongsTo(__NAMESPACE__.'\Conjunto','conjunto_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagos()
    {
        return $this->hasMany(__NAMESPACE__.'\Pago');
    }
}