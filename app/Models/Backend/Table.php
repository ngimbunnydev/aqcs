<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Table extends Model
{
	protected $table = 'pos_table';
	protected $primaryKey = 'table_id';
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
        return DB::table("pos_table")
        ->select(['table_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('trash', '<>', 'yes')
         ->orderBy('ordering');

    }

}
