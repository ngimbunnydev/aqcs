<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Systemconfig extends Model
{
	protected $table = 'sys_config';
	protected $primaryKey = 'sycog_id';
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

    public function scopeGetpages($query, $dflang)
    {
        return DB::table("cms_pages")
        ->select(['p_id', 'p_name'])
        ->where('trash','<>','yes')
        ->orderBy('p_id');

    }


}
