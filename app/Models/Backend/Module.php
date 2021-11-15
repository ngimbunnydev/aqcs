<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Module extends Model
{
	protected $table = 'cms_module';
	protected $primaryKey = 'md_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/

  /*
   * by phearun
   * get articles with own module
   * 24/06/2021
   */
  public function articles(){
    return $this->hasMany(Article::class, 'md_id');
  }
  
  /*
   * by phearun
   * get child module with own module
   * 24/06/2021
   */
  public function childModules(){
    return $this->hasMany(Modules::class, 'md_id');
  }
  
}
