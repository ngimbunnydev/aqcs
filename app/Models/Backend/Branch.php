<?php
namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;



class Branch extends Model
{
	protected $table = 'pos_branch';
	protected $primaryKey = 'branch_id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/

    public function scopeGettable()
    {
        return $this->table;

    }

    public function scopeBranchall($query, $dflang, $except=0)
    {

        $where=[['branch_id', '<>', $except]];

        return DB::table($this->table)
        ->select(['branch_id','address', 'phone' , 'pic', DB::raw("JSON_UNQUOTE(title->'$.".$dflang."') as title")])
        ->where($where)
        ->get()->keyBy('branch_id')->toArray();

    }


   


}
