<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Article extends Model
{
	protected $table = 'cms_article';
	protected $primaryKey = 'a_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


    public function scopeGetcategory($query, $dflang)
    {
        return DB::table("cms_category")
        ->select(['c_id','parent_id','ab_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where('trash','<>','yes')
        ->orderBy('ordering')
        ->orderBy('c_id');

    }


    public function scopeGetpages($query, $dflang)
    {
        return DB::table("cms_pages")
        ->select(['p_id', 'p_name'])
        ->where('trash','<>','yes')
        ->orderBy('p_id');

    }

    public function scopeGetmodule($query, $dflang)
    {
        return DB::table("cms_module")
        ->select(['md_id',DB::raw("JSON_UNQUOTE(moduletitle->'$.".$dflang."') as title")])
        ->where('moduletype','form')
        ->orderBy('ordering');

    }

    /*public function scopeInsersubtable($query,$tablename,$datainsert)
    {

        DB::table($tablename)->insert($datainsert);
        return DB::getPdo()->lastInsertId();
    }*/



    public function hello(){
    	//return $this->fillabl. ' Model';
    	
    	//$user = $this->find(1);
    	//return $user->title;
        return 'Hello';
    }
  
    public function articleFiles(){
      return $this->hasMany(ArticleFile::class, 'obj_id', 'a_id');
    }
  
    public function articleDetails(){
      return $this->hasMany(ArticleDetail::class, 'a_id');
    }


}
