<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 24/11/2015
 * Time: 7:38 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abono extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['id' ,'valor', 'pago_id', 'forma_pago'];

    protected $table = 'abonos';

    public $incrementing = true;

    public $primaryKey = 'id';


    // relaciones

    public function pago()
    {
        return $this->hasOne(__NAMESPACE__.'\Pago');
    }

}