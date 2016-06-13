<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DB;

/**
 * Class Abono
 * @package App
 */
class Abono extends Model
{
    use SoftDeletes;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot() 
    {
        parent::boot();
        static::creating(function($table) {
            $max_id = DB::select("select max(id) as value from abonos;");
            $table->id = $max_id[0]->value + 1;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' ,'valor', 'pago_id', 'forma_pago'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'abonos';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pago()
    {
        return $this->hasOne(__NAMESPACE__.'\Pago');
    }

}