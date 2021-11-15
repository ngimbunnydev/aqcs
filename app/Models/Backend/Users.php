<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Users extends Model
{
	protected $table = 'users';
	protected $primaryKey = 'id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/

  public function systracks()
  {
      return $this->hasMany('App\Models\Backend\Systracking', 'userid');
  }
  public function getFullNameAttribute()
  {
     return "{$this->latinname} {$this->nativename}";
  }
}
