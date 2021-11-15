<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Region extends Model
{
	protected $table = 'cms_region';
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
  
     public function scopeGetcategory($query, $dflang, $except=0)
    {

        $where=[['trash','<>','yes'],['location_id', '<>', $except]];

        return DB::table("cms_region")
        ->select(['location_id','parent_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('location_id');

    }
  

    public function scopeGetregion($query, $dflang, $except=0)
    {

        $where=[['trash','<>','yes'], ['parent_id', '=', 0],['location_id', '<>', $except]];

        return DB::table("cms_region")
        ->select(['location_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('location_id');

    }
  
     public function scopeGetmodule($query, $dflang)
    {
        return DB::table("cms_module")
        ->select(['md_id',DB::raw("JSON_UNQUOTE(moduletitle->'$.".$dflang."') as title")])
        ->where('moduletype','form')
        ->orderBy('ordering');

    }

}
