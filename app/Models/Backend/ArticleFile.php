<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class ArticleFile extends Model
{
	protected $table = 'cms_articlefile';
	protected $primaryKey = 'objf_id';
    public $timestamps = false;

    // public function scopeGetdetails($query, $id, $dflang)
    // {
    //     return DB::table($this->table)
    //    // ->select()
    //     ->join('cms_articledetail','cms_article.a_id','=','cms_articledetail.a_id')
    //     ->where('cms_article.'.$this->primaryKey,$id)
    //    ->where('lg_code','=',$dflang)
    //     ->where('trash','<>','yes')
    //     ->orderBy('cms_article.'.$this->primaryKey);

    // }


}
