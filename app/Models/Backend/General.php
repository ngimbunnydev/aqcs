<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class General extends Model
{
	protected $table = 'cms_general';
	protected $primaryKey = 'g_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


    public function scopeGetpages($query, $dflang)
    {
        return DB::table("cms_pages")
        ->select(['p_id', 'p_name'])
        ->where('trash','<>','yes')
        ->orderBy('p_id');

    }


}
