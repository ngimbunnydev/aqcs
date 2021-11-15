<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Blogs extends Model
{
	protected $table = 'cms_blogs';
	protected $primaryKey = 'b_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


}
