<?php
namespace App\Plugins\Filemanager;

use Illuminate\Database\Eloquent\Model;
use DB;

class Filemanager extends Model
{
	protected $table = 'cms_filemanager';
	protected $primaryKey = 'f_id';
    /*protected $fillable = [
        'f_id',
        'fc_id',
        'media_type',
        'media',
        'mwidht',
        'mheight',
        'blong_obj',
        'blongto'
    ];*/

    /*public function Filecategory()
    {
        return $this->belongsTo(Filecategory::class);
    }*/

    public function scopePopular($query,$condition=[],$categoryid=0,$blongobj='public')
    {
        $categoryid=(int)$categoryid;
        if(empty($categoryid))
        {
            $categoryid=0;
        }

        if(empty($condition))
        {
            $condition_ext='f_id<>0';
        }
        else
        {
            $condition_ext="SUBSTRING_INDEX(media,'.',-1) in ('".implode("','", $condition)."')";
        }
        
        return $query->select([DB::raw('SQL_CALC_FOUND_ROWS *')])
                        ->whereRaw($condition_ext)
                        ->where('blongobj', '=', $blongobj)
                        ->where('fc_id','=',$categoryid)
                        ->orderBy($this->primaryKey,'DESC');

    }

    public function scopeGetfiletoobj($query,$fileslist=[0],$categoryid=0,$blongobj='public')
    {
        $categoryid=(int)$categoryid;
        if(empty($categoryid)){
            $categoryid=0;
        }
       
       /* return $query
                        ->where('blongobj', '=', $blongobj);/*
                        ->whereIn($this->primaryKey,[1])
                        ->where('fc_id','=',$categoryid)
                        ->orderBy($this->primaryKey,'DESC');*/

                        return $query
                        ->whereIn($this->primaryKey,$fileslist)
                        ->where('blongobj', '=', $blongobj)
                        #->where('fc_id','=',$categoryid)
                        ->orderBy($this->primaryKey,'DESC');

        

    }

    


    public function scopeInserfiletoobj($query,$tablename,$datainsert)
    {

        DB::table($tablename)->insert($datainsert);
        return DB::getPdo()->lastInsertId();
    }

    public function scopeGetfileofobj($query,$tablename,$objid=[0],$categoryid=0)
    {
        $categoryid=(int)$categoryid;
        if(empty($categoryid)){
            $categoryid=0;
        }
       
        return DB::table($tablename)
        ->whereIn('obj_id', $objid)
        ->where('fc_id','=',$categoryid)
        ->orderBy('objf_id','ASC');

        

    }

    public function scopeGetfileinfo($query,$tablename,$objf_id)
    {
        return DB::table($tablename)
        ->where('objf_id', '=', $objf_id)
        ->offset(0)
        ->limit(1);

        //select('name','surname')->where('id', 1)->get();

    }

    public function scopeUpdatefileinfo($query,$tablename,$objf_id,$updateinfo)
    {
        return DB::table($tablename)
        ->where('objf_id', '=', $objf_id)
        ->update($updateinfo);
    }

    public function scopeUpdatefatherimage($query,$tablename, $idfield,$idvalue,$updateinfo)
    {
        return DB::table($tablename)
        ->where($idfield, '=', $idvalue)
        ->update($updateinfo);
    }

    public function scopeUpdateblank($query,$tablename,$field,$obj_id)
    {
        return DB::table($tablename)
        ->where('obj_id', '=', $obj_id)
        ->update([$field=>'']);
    }


    public function scopeSeekcover_bg($query,$tablename,$field,$obj_id)
    {
        $result= DB::table($tablename)
        ->select('objf_id')
        ->where($field,'yes')
        ->where('obj_id', $obj_id)->first();
        if($result)
        return $result->objf_id;
        else return '';
    }

    public function scopeRemovefileobj($query,$tablename,$fileid)
    {
        return DB::table($tablename)->where('objf_id', '=', $fileid)->delete();

    }

}
