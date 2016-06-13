<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Conjunto
 * @package App
 */
class Conjunto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'nombre','direccion','ciudad'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'conjuntos';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propiedad()
    {
        return $this->belongsTo(__NAMESPACE__.'\Propiedad');
    }
}
