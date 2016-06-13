<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipo_Propiedad
 * @package App
 */
class Tipo_Propiedad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'nombre','descripcion'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tipo_propiedades';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propiedad()
    {
        return $this->hasMany(__NAMESPACE__.'\Propiedad');
    }
}