<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Color extends Model
{
	protected $table = 'pos_color';
	protected $primaryKey = 'cl_id';
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

    public function scopeGetcolor($query, $dflang)
    {
        return DB::table("pos_color")
        ->select(['cl_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title"),'code'])
        ->where('trash', '<>', 'yes')
         ->orderBy('cl_id');

    }


   


}
