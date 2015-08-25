<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_deuda extends Model
{
    protected $fillable = ['id' ,'concepto','valor','fecha_inicial','fecha_final','descripcion'];

    protected $table = 'tipo_deudas';

    public function deuda()
    {
        return $this->belongsTo(__NAMESPACE__.'\Deuda');
    }
}