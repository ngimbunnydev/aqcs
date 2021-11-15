<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Userlevel extends Model
{
	protected $table = 'user_level';
	protected $primaryKey = 'level_id';
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

    public function scopeGetlevel($query, $level_id)
    {
        if(empty($level_id)) return [];
        $level = DB::table("user_level")
        ->where('level_id', '=', $level_id)
        ->where('level_status', '=', 'yes')
        ->where('trash', '<>', 'yes')
         ->get();

         //dd($level);
         if($level->isEmpty()) return false;
         else return $level[0];

    }


}
