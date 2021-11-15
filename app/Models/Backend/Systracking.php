<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;

class Systracking extends Model
{
	protected $table = 'sys_tracking';
	protected $primaryKey = 'track_id';
  public $timestamps = false;
  
  public function scopeGettable()
  {
     return $this->table;
  }
  
  public function user()
  {
      return $this->belongsTo('App\Models\Backend\Users', 'userid');
  }
  
}
