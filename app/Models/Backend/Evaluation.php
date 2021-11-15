<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Evaluation extends Model
{
	protected $table = 'aqcs_evaluation';
	protected $primaryKey = 'evaluation_id';
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
        return DB::table("aqcs_evaluation")
        ->select(['evaluation_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('trash', '<>', 'yes')
         ->orderBy('ordering');

    }

}
