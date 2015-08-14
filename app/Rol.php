<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = ['id' ,'nombre','descripcion'];

    protected $table = 'roles';

    public function usuario()
    {
        return $this->belongsTo(__NAMESPACE__.'\Usuario');
    }
}