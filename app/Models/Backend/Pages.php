<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Pages extends Model
{
	protected $table = 'cms_pages';
	protected $primaryKey = 'p_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/



    public function scopeGeneratemenu($query, $dflang)
    {

        $where=[['trash','<>','yes']];

        return DB::table("cms_category")
        ->select([$this->primaryKey,'parent_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('c_id');

    }


    public function hello(){
    	//return $this->fillabl. ' Model';
    	
    	//$user = $this->find(1);
    	//return $user->title;
        return 'Hello';
    }


}
