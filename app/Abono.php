<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 24/11/2015
 * Time: 7:38 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = ['id' ,'valor', 'pago_id', 'forma_pago'];

    protected $table = 'abonos';

    public $incrementing = true;

    public $primaryKey = 'id';

    protected $hidden = array(
        'deleted_at',
        'created_at',
        'updated_at'
    );


    // relaciones

    public function pago()
    {
        return $this->hasOne(__NAMESPACE__.'\Pago');
    }

}