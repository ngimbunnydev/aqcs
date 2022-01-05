<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Frontlivemap extends Model
{
	protected $table = 'airqty_livemap';
	protected $primaryKey = 'location_id';
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
