<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Caption extends Model
{
	protected $table = 'cms_caption';
	protected $primaryKey = 'ct_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


    public function scopeGetcategory($query, $dflang, $except=0)
    {

        $where=[['trash','<>','yes'],['ct_id', '<>', $except]];

        return DB::table("cms_caption")
        ->select(['ct_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ct_id');

    }


}
