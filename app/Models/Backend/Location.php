<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Location extends Model
{
	protected $table = 'aqcs_location';
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
  
  public function scopeGetdata($query, $dflang)
    {
        return DB::table("aqcs_location")
        ->select(['table_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('trash', '<>', 'yes')
         ->orderBy('ordering');

    }

}
