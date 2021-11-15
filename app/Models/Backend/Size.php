<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Size extends Model
{
	protected $table = 'pos_size';
	protected $primaryKey = 's_id';
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

    public function scopeGetsize($query, $dflang)
    {
        return DB::table("pos_size")
        ->select(['s_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('trash', '<>', 'yes')
         ->orderBy('s_id');

    }


   


}
