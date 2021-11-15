<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class ArticleDetail extends Model
{
	protected $table = 'cms_articledetail';
	protected $primaryKey = 'at_id';
  public $timestamps = false;
  
}
