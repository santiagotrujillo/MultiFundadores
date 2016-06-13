<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rol
 * @package App
 */
class Rol extends Model
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
    protected $table = 'roles';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(__NAMESPACE__.'\Usuario');
    }
}