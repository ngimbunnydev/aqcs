<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Airqualitymonitoring extends Model
{
	protected $table = 'aqcs_airqm';
	protected $primaryKey = 'aqm_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/

    public function scopeGettable()
    {
        return $this->table;

    }


}
