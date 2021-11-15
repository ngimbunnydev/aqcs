<?php

	/**
	 * @param Array elements[id,parent_id,name,...]
	 * @param Array $defaultField(id_fieldname,parent_fieldname)
	 * @param Int parentId
	 * @return Array
	 */
	function buildArrayTree(array $elements,$defaultField=['id','parent_id'], $parentId = 0) {
	    $tree = array();
	    foreach ($elements as $element) {
	        if ($element[$defaultField[1]] == $parentId) {
	            $children = buildArrayTree($elements, $defaultField, $element[$defaultField[0]]);
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $tree[] = $element;
	        }
	    }

	    return $tree;
	}/**@endfun**/


	function newName($tryname,$elements,$key,$ind=1){
		list($fixtitle)=explode('(', $tryname);
		foreach ($elements as $element) {
			if($element[$key]==$tryname){
				$newname=trim($fixtitle)." (".(string)$ind.")";
				$ind++;
				$tryname=newName($newname,$elements,$key,$ind);
			}
		}
		return $tryname;
	}/**@endfun**/

	function copyTitle($old_title,$new_title,$model,$field) 
	{
		$check_title= $model->select(DB::raw( $field. " AS title"
                                                )
                                        )
										->where(DB::raw( $field),'=',$old_title)
										->get()->toArray();

		if(!empty($check_title)){
			$new_title="copy of ".$check_title[0]['title'];
			$new_title=copyTitle($new_title,$new_title,$model,$field);
		}
		else{ $new_title=$old_title;}
		return $new_title;
	}/**@endfun**/

	function totalFoundRows(){
		return DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total';"))[0]->total;
	}

	function getMaxId($tablename,$primarykey='id')
    {
        return DB::table($tablename)->max($primarykey);
    }/**@endfun**/

    function selectDataTable($tablename,$where)
    {

        return DB::table($tablename)
        ->where($where);
        
    }/**@endfun**/

    function insertDataTable($tablename,$datainsert)
    {

        DB::table($tablename)->insert($datainsert);
        return DB::getPdo()->lastInsertId();
    }/**@endfun**/

    function updateDataTable($tablename,array $where, array $update )
    {

		return DB::table($tablename)->where($where)->update($update);

    }/**@endfun**/

    function deleteDataTable($tablename,array $where)
    {

        return DB::table($tablename)->where($where)->delete();
    }/**@endfun**/


    function get_parent($tablename,$id_field,$parent_field,$chile_id,$result=""){
        $id = empty($id) ? 0 : (int) $id;
        $new_parent = DB::table($tablename)
        ->select([$parent_field])
        ->where($id_field,'=',$chile_id);
        $new_parent = $new_parent->value([$parent_field]);
		  
        if(!empty($new_parent)){
            $parent=get_parent($tablename,$id_field,$parent_field,$new_parent,$new_parent.",");
            $result.=$parent;
        }
        return $result;
    }//end function
    
    function get_child($tablename,$id_field,$parent_field,$parent_id,$result=""){
        $parent_id = empty($parent_id) ? 0 : (int) $parent_id;
        //$new_parent=quiries::group_concat($id_field,$table,$parent_field."=".$parent_id);
        $new_parent = DB::table($tablename)
        ->select(DB::raw('group_concat('.$id_field.') as '.$id_field))
        ->where($parent_field,'=',$parent_id);
        $new_parent = $new_parent->value([$id_field]);
        if(!empty($new_parent)){
           
            $arr_parent=explode(",",$new_parent);
            $count=count($arr_parent);
            for($i=0;$i<$count;$i++){
                $result.= get_child($tablename,$id_field,$parent_field,$arr_parent[$i],$new_parent.",");
            }
            
        }
        return $result;
    }//end function

    function productsubform($subpd_id=[],$productmodel, $dflang)
		{
			/*retriev some records of product for add-stock adjust-stock madewith invoice quotation etc
			*/
			$prodctsarray=$products_arr=[];
               if(count($subpd_id)>0)
                {
                    $subpd_ids = $subpd_id;

                    $products=$productmodel->plookupquery($dflang);
                    $products = $products->where(function($query) use ($subpd_ids){
                         $query->whereIn('pd_id', $subpd_ids);

                     });

                    $products = $products->get()->toArray();
                    if($products)
                    {
                        foreach($products as $r){
                          
                            $products_arr[$r->id] = $r;
                        }
                        
                        for($i=0; $i<count($subpd_id); $i++)
                        {
                            if(!empty($subpd_id[$i]))
                            {
                                $prodctsarray[$i] = $products_arr[$subpd_id[$i]];
                            }else
                            {
                                $prodctsarray[$i] = [];
                            }
                            
                        }

                    }
                    

                }
            return $prodctsarray;

	}/*end func*/

	function bulkQtyForValidation($results, $request)
	{
		$bulkqty = null;
        $results = $results->get()->toArray();
        $ind=0;
        foreach ($results as $key => $row) {
          $qty=[];
          if(config('sysconfig.usingsizecolor')=='no')
          {
            $name = 'txt_qty'.$row->id.'-0-0';
            $ele = $request->input($name)??'';
            array_push($qty,abs($ele));
            $bulkqty['bulkqty'.$ind++]=$qty;
          }
          else
          {
            $sizes =  explode(',',$row->sizes);
            $colors = explode(',',$row->colors);
            
            foreach ($sizes as $sizeid) {
                $sizeid = !empty($sizeid)?$sizeid:'0';

                foreach ($colors as $colorid) {

                    $colorid = !empty($colorid)?$colorid:'0';
                    $name = 'txt_qty'.$row->id.'-'.$sizeid.'-'.$colorid;
                    $ele = $request->input($name)??'';
                    array_push($qty,abs($ele));
                }
            }

             $bulkqty['bulkqty'.$ind++]=$qty;
          }
           //        

        }

        return $bulkqty;

	}/*end func*/

    function bulkQtyForSetinfo($results, $request, $unit_amount=1)
    {
        /*madewithinfo is info of made-with's product*/
        /*parentQty is qty that inputed by user*/

        
        $units = config('calunit');

        $bulkqty = null;
        $bulkqty_unit = null;
        $results = $results->get()->toArray();
        
        foreach ($results as $key => $row) {
                if(config('sysconfig.usingsizecolor')=='no')
                {
                    $sizes =  [0];
                    $colors = [0];
                }
                else
                {
                    $sizes =  explode(',',$row->sizes);
                    $colors = explode(',',$row->colors);
                }
                    
                    //$unit_amount =$units[$row->unt_id];
                    $qty=[];
                    $qty_unit=[];
                    foreach ($sizes as $sizeid) {
                        $sizeid = !empty($sizeid)?$sizeid:'0';

                        foreach ($colors as $colorid) {

                            $colorid = !empty($colorid)?$colorid:'0';
                            $name = 'txt_qty'.$row->id.'-'.$sizeid.'-'.$colorid;
                            $ele =$request->input($name)??0;
                            $ele = abs($ele);
                            //array_push($qty,$ele);
                            if(!empty($ele))$qty[$sizeid.'-'.$colorid]=$ele;

                            $qty_unit[$sizeid.'-'.$colorid]=$ele*$unit_amount;
                        }
                    }

                     $bulkqty['bulkqty.'.$row->id]=$qty;

                     $bulkqty_unit['bulkqty.'.$row->id]=$qty_unit;

        }

        return ['bulkqty'=>$bulkqty, 'bulkqty_unit'=>$bulkqty_unit];

    }/*end func*/

    function productAccount($in_ids){
      
       $in_ids = array_map(
          function($value) { return (int)$value; },
          $in_ids
      );
      
      $coa = DB::table('cms_product')
      ->select(DB::raw('pd_id, cms_pcategory.accno_id as accno_id, accno_idrpm, accno_idcogs'))
      ->join('cms_pcategory', 'cms_product.c_id', '=', 'cms_pcategory.c_id')
//       ->join('pos_accountno', 'cms_pcategory.accno_id', '=', 'pos_accountno.accno_id')
//       ->join('pos_accounttype', 'pos_accountno.acctype_id', '=', 'pos_accounttype.acctype_id')
      ->WhereIn('pd_id', $in_ids);
      
      $coa_unique=[];
      if($coa){
        $coa = $coa->get();
        foreach($coa as $key => $record){
          array_push($coa_unique, $record->accno_id, $record->accno_idrpm, $record->accno_idcogs);
        }
        $coa_unique = array_unique($coa_unique);
        //	natureside
        $natureside = natureside($coa_unique);
        
        $coa = $coa->keyBy('pd_id')->toArray();
        return ['coa' => $coa, 'natureside' => $natureside];
      }
      return null;
      
    } 

    function natureside($coa_unique){
        //$coa_unique. is ARRAY
        //	natureside
        $natureside = DB::table('pos_accountno')
        ->select(DB::raw('accno_id, pos_accountno.code as code, pos_accountno.title as title, LOWER(natureside) as natureside'))
        ->join('pos_accounttype', 'pos_accountno.acctype_id', '=', 'pos_accounttype.acctype_id')
        ->WhereIn('accno_id', $coa_unique);
        if($natureside){
          $natureside = $natureside->get()->keyBy('accno_id')->toArray();
        }
        return $natureside;
    }

    function save_gj($data){
      if(empty($data)){
        $save = false;
      }
      else if(count($data)>0){
        if(empty($data[0])){
          $save = false;
        }
        else{
          $save =  DB::table('account_generaljournal')->insert($data);
        }
      }
      
      if($save){
        foreach($data as $row){
          if(!empty($row)){
            $accno_id = $row['accno_id'];
            $dr_amount = $row['dr_amount'];
            $cr_amount = $row['cr_amount'];
            if($dr_amount==0){
              $natureside = DB::table('pos_accountno')->where('accno_id', $accno_id)->update(['balance'=>DB::raw( 'balance - '. $cr_amount)]);
            }
            else{
              $natureside = DB::table('pos_accountno')->where('accno_id', $accno_id)->update(['balance'=>DB::raw( 'balance + '. $dr_amount)]);
            }
          }
        }
      }
      
      return $save;
      
    }/*end func*/

  function gj_totrash($ref, $ref_id){
    
      return  DB::table('account_generaljournal')
        ->whereRaw("lower(ref)='".strtolower($ref)."'")
        ->where('ref_id',(int)$ref_id)
        ->update(['trash'=>'yes']);
    
  }

  function gj_delete($ref, $ref_id){
    
      return  DB::table('account_generaljournal')
        ->whereRaw("lower(ref)='".strtolower($ref)."'")
        ->where('ref_id',(int)$ref_id)
        ->delete();
    
  }

    
?>