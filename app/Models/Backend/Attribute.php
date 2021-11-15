<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Attribute extends Model
{
	protected $table = 'cms_attribute';
	protected $primaryKey = 'ab_id';
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


      public function scopeGetparent($query, $dflang)
    {
        return DB::table($this->table)
        ->select([$this->primaryKey, DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('parent_id','=',0)
        ->where('trash','<>','yes')
         ->orderBy('ordering');

    }


}
