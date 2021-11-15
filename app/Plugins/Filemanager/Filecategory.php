<?php
namespace App\Plugins\Filemanager;

use Illuminate\Database\Eloquent\Model;

class Filecategory extends Model
{
	protected $table = 'cms_filecategory';
	protected $primaryKey = 'fc_id';
    protected $df_field=['id'=>'fc_id','pid'=>'parent_id','name'=>'c_name'];

    /*public function filemanager()
    {
        return $this->hasMany(Filemanager::class);
    }*/

    public function scopeGetall($query,$calledby='public',$objid=0)
    {
        //return $query->orderBy('ordering')->orderBy('fc_id');
        if($objid==0)
        {
            $where=['blongobj'=>$calledby];
            /*use this $where=[['blongobj','=',$calledby]]; if define a operator*/
        }
        else
        {
            $where=['blongobj'=>$calledby,'objid'=>$objid];
        }

        return $query
                ->where($where)
                ->orderBy('ordering')
                ->orderBy('fc_id');
    }

    public function scopeGetchildname($query,$parent_id=0)
    {
        return $query->select('c_name')->where('parent_id', '=', $parent_id);
    }

}
