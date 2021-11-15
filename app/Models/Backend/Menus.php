<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Menus extends Model
{
	protected $table = 'cms_menus';
	protected $primaryKey = 'm_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


    public function scopeGetcategory($query, $dflang, $except=0)
    {

        $where=[['trash','<>','yes'],['c_id', '<>', $except]];

        return DB::table("cms_category")
        ->select(['c_id','parent_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('c_id');

    }


    public function scopeGetpcategory($query, $dflang, $except=0)
    {

        $where=[['trash','<>','yes'],['c_id', '<>', $except]];

        return DB::table("cms_pcategory")
        ->select(['c_id','parent_id',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('c_id');

    }


    public function scopeGeneratemenu($query, $dflang, $menugroup=0)
    {

        $where=[['trash','<>','yes'],['mgroup', '=', $menugroup]];

        return DB::table("cms_menus")
        ->select(['m_id','parent_id','linktype','p_id','linkto','target','isindex','tags',DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->orderBy('ordering')
        ->orderBy('m_id');

    }
  
  /*
   * by phearun
   * get children
   * 25/06/2021
   */
  public function children()
	{
		return $this->hasMany(Menus::class, 'parent_id', 'm_id')->with('children');
	}

}
