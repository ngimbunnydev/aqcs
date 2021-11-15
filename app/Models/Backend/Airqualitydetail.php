<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Airqualitydetail extends Model
{
	protected $table = 'aqcs_airqmdetail';
	protected $primaryKey = 'aqmd_id';
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
