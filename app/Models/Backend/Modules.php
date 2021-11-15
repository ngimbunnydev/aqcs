<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Modules extends Model
{
	protected $table = 'cms_modules';
	protected $primaryKey = 'mds_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/


     


}
