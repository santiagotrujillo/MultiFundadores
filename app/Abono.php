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

use \DB;

class Abono extends Model
{
    use SoftDeletes;

    public static function boot() {
        parent::boot();
        static::creating(function($table)  {
            $max_id = DB::select("select max(id) as value from abonos;");
            $table->id = $max_id[0]->value + 1;
        });
    }
    
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