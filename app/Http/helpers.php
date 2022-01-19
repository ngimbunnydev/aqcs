<?php
	function url_builder($routename,$path,$qstring=array(),$mix=true){
		if(is_string($path))
		{
			return $path;
		}

		$c=count($path);
		$url=array_merge($path, array_filter($qstring));
		if($mix)
			return route($routename.$c, $url);
		else
			return route($routename, $url);
	}


	function friendly_builder($arr){
		//[language,title,page,id,act]
		return [$arr['lan'], $arr['title'], $arr['page'], $arr['id'], $arr['act']];
	}


	function a_builder($attribute, $content=true){
              $a = '<a class="'.$attribute['class'].'" href="'.$attribute['href'].'" title="'.$attribute['title'].'" rel="'.$attribute['rel'].'" target="'.$attribute['target'].'" '.$exraattr.'>';
              if($content)
                     return $a.$attribute['title'].'</a>';
              else
                     return $a;
       }

	function generate_menuqrs($element,$pagelist,$lan,$setting=[]){
		$act='';
		if($element['p_id']==0) { //p_id = Page ID
			if($element['linktype']=='custom'){
				$pagename = $element['linkto']; $element['linkto']=''; $act='';
			}
			else{
				$pagename = $element['linktype'];
			}
					
		}else{
			$pagename = $pagelist[$element['p_id']];
		}

		if(stripos($pagename,".")!==false OR stripos($pagename,":")!==false){
		 	$qrs=$pagename;
		}
		else
		{
			$qrs =friendly_builder(['lan'=>$lan,'title'=>str_replace(' ', '-', $element['title']),'page'=>$pagename.stripos($pagename,"."),'id'=>$element['linkto'],'act'=>$act]);
		}

			


			$seorel = $element['isindex']=='no'?'nofollow':'';
			$attribute =['href'=>$qrs, 'seorel'=>$seorel, 'target'=>$element['target'], 'title'=>$element['title']];
			return $attribute;
	}

	function str_clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	function nav_checkactive($obj,$activeobj, $open=''){
		/*Navegation check Active*/
    $showhide_class = '';
    if(count($obj)==1){
      $showhide_class ='showhide_'.$obj[0];
    }
		return in_array($activeobj, $obj) ? $showhide_class.' '.'active'.' '.$open : $showhide_class;
	}

	function str_sanitize($str_input){
        $str_input = strip_tags($str_input, config('ccms.allowtags'));
        $str_input=str_replace('\\r\\n','', $str_input);
        $str_input = htmlentities($str_input, ENT_QUOTES);
        $str_input=str_replace('\\r\\n','', $str_input);
        return $str_input;
    }

	function sanitize_filename($filename, $forceextension=""){
		/*
		1. Remove leading and trailing dots
		2. Remove dodgy characters from filename, including spaces and dots except last.
		3. Force extension if specified
		*/

			$defaultfilename = "none";
			$dodgychars = "[^0-9a-zA-z()_-]"; // allow only alphanumeric, underscore, parentheses and hyphen
			$filename = str_sanitize($filename);
			$filename = preg_replace("/^[.]*/","",$filename); // lose any leading dots
			$filename = preg_replace("/[.]*$/","",$filename); // lose any trailing dots
			$filename = $filename?$filename:$defaultfilename; // if filename is blank, provide default

			$lastdotpos=strrpos($filename, "."); // save last dot position
			$filename = preg_replace("/$dodgychars/","_",$filename); // replace dodgy characters
			$afterdot = "";
			if ($lastdotpos !== false) { // explode into name and extension, if any.
			$beforedot = substr($filename, 0, $lastdotpos);
			if ($lastdotpos < (strlen($filename) - 1))
			$afterdot = substr($filename, $lastdotpos + 1);
			}
			else // no extension
			$beforedot = $filename;

			if ($forceextension)
			$filename = $beforedot . "." . $forceextension;
			elseif ($afterdot)
			$filename = $beforedot . "." . $afterdot;
			else
			$filename = $beforedot;

			return $filename;
		}/*end func*/

		function recordInfo($current, $perpage, $total, $lastpage=0){
			#use for Pagination, it will inform other info of pagination#
			$from= ($current==1)? ($total==0)?0:1 : (($current-1)*$perpage)+1;

			$to = ($current==1)? $perpage  :($current*$perpage);
			if($to>$total)$to=$total;
			return ['from'=>$from, 'to'=>$to, 'total'=>$total, 'perpage'=>$perpage, 'lastpage' => $lastpage];
		}

		function get_between_strings($start, $end, $content){
			$r = explode($start, $content);
		    if (isset($r[1])){
		        $r = explode($end, $r[1]);
		        return $r[0];
		    }
		    return '';
		}


		function render($__php, $__data)
		{
		    $obLevel = ob_get_level();
		    ob_start();
		    $__data['__env'] = app(\Illuminate\View\Factory::class);
		    extract($__data, EXTR_SKIP);

		    try {
		        eval('?' . '>' . $__php);
		    } catch (Exception $e) {
		        while (ob_get_level() > $obLevel) ob_end_clean();
		        throw $e;
		    } catch (Throwable $e) {
		        while (ob_get_level() > $obLevel) ob_end_clean();
		        throw new FatalThrowableError($e);
		    }
		    return ob_get_clean();
		}/*end func*/


	    function calUnit(array $elements, $parent_id=0, $calunit=[]) {
	        foreach($elements as $element)
	        {
	        	if($parent_id==0){
	        		$calunit[$element['unt_id']]=$element['qty'];
	        	}
	        	else{
	        		
	        		$calunit[$element['unt_id']]=$calunit[$parent_id]*$element['qty'];
	        	}
	        	
	            if (!empty($element['children'])) {
	            	$parent =$element['unt_id'];
	               	$calunit=	Calunit($element['children'],$parent, $calunit);
	     
	            }
	            

	        }

	        return $calunit;
	    }/**@endfun**/

      function unitInfo(array $elements) {
          
          foreach($elements as $element)
	        {
            $info =[];
            $parent_id = $element['parent_id'];
	        	if($parent_id!=0){
              
	        		$info = unitParent($elements, $element,1, $parent_id, $info);
	        	}
	  
            $element['unitinfo'] = $info;
            $elements[$element['unt_id']] = $element;
	        	  
	        }
        
          return $elements;
        
	    }/**@endfun**/

      function unitParent(array $elements, $element, $multiby, $parent_id, $info=[]){
        $parent = $elements[$parent_id];
        $info[$parent_id] = $element['qty']*$multiby;
        if($parent['parent_id']!=0){
          $multiby = $element['qty'];
          $info = unitParent($elements,$parent, $multiby, $parent['parent_id'], $info);
        }
        return $info;
        
      }

	    function retrieveMadewithID($all_madewith)
	    {
	    	/*$all_madewith => format:
	    	0 => "{"1-0-0":"0.2","4-0-0":"0.3"}"
	    	*/

                $all_madewithid=[];

                foreach ($all_madewith as $key => $value) {
                   
                    $getmadewith = json_decode($value,true);
                    if($getmadewith!=null){
                      $ind = array_keys($getmadewith);
                      $all_madewithid=array_merge($all_madewithid,$ind);
                    }
                    

                }
                $count=count($all_madewithid);
                for($i=0; $i<$count; $i++)
                {
                    $xplode = explode('-', $all_madewithid[$i]);
                    $all_madewithid[$i]=$xplode[0];
                }

            return $all_madewithid;
	    }/**@endfun**/

		function setSubform($request,$ProductModel, $masterfprimarykey, $subfprimarykey, $newid, $dflang, $forbulkqty=true)
		{
			/*subtable*/
	        $subtableData=[];  
          $madewithData=[];
          $all_madewithData=[];
	        $transfertableData=[];
          $transferstock=[];
	        $id_tostocks = [];
	        $total_qty=0;

	        if ($request->has('subpd_id'))
	        {
	        	  //$add_date = date("Y-m-d H:i:s");
              $add_date=!empty($request->input('add_date'))?date("Y-m-d H:i:s", strtotime($request->input('add_date'))):date("Y-m-d H:i:s");
              $subpd_id = $request->input('subpd_id');
              $transfer_id = $request->input('subtransferto');
            
	            if($forbulkqty)$subpd_id = array_unique($subpd_id);
	            $numrecord=count($subpd_id);
	            if($numrecord>1)
	            {
	                $numrecord-=1;
	                array_pop($subpd_id);
	                $results = $ProductModel->plookupquery($dflang);
	                $results = $results->whereIn('pd_id', $subpd_id);
                 
                  if(!empty($transfer_id))
                  {
                    array_pop($transfer_id);
                    $transfer_result = $ProductModel->select('pd_id','unt_id')
                      ->whereIn('pd_id', $transfer_id)
                      ->pluck('unt_id', 'pd_id')
                      ->toArray();
                  }
                  

	                if($forbulkqty)
	                {
	                	/*add stock adjuststock*/
	                	$bulkqtys = bulkQtyForSetinfo($results, $request);
	                	$bulkqty = $bulkqtys['bulkqty'];
	                	$bulkqty_unit = $bulkqtys['bulkqty_unit'];
	                }
	               
	                

	                /*way to do with made-with*/
	                $all_madewith = $results->pluck('madewith')->toArray();
	                $all_madewithid=retrieveMadewithID($all_madewith);
	                if(!empty($all_madewithid))
	                {
	                    $madewithresult = $ProductModel->plookupquery($dflang);
	                    $madewithresult = $madewithresult->whereIn('pd_id', $all_madewithid); 
	                    $madewithresult = $madewithresult->get()->keyBy('id')->toArray();
	                }
	                /***/

	                $records = $results->get()->keyBy('id')->toArray();
	                //$dbname = \DB::connection()->getDatabaseName();
	                $units = config('calunit');
	                $qtyinput = $qtyinput_unit = [];
	                for($i=0; $i<$numrecord; $i++)
	                {

	                	$pd_index=$subpd_id[$i];

	                	if(!$forbulkqty)
		                {
			               	$subsize = !empty($request->input('subsize')[$i]) ? $request->input('subsize')[$i] :0;
                      $subcolor = !empty($request->input('subcolor')[$i]) ? $request->input('subcolor')[$i] :0;
                      $inputqty = !empty($request->input('subqty')[$i]) ? $request->input('subqty')[$i] :0;
                      $key = $subsize.'-'.$subcolor;

                      //$unit_amount =empty($records[$pd_index]->unt_id)?1:$units[$records[$pd_index]->unt_id]??1;
                      $unit_amount = !empty($request->input('convert_qty')[$i]) ? $request->input('convert_qty')[$i] :1;

                      $qtyinput[$i][$key]=abs($inputqty);
                      $qtyinput_unit[$i][$key]=abs($inputqty*$unit_amount);
                    }

	                    /*define made-with record*/
	                    if(!empty($pd_index) && strlen($records[$pd_index]->madewith)>2)
	                    {
	                            $madewithData=[];
	                            $parent_product =$records[$pd_index]->id;
	                            
                               if(!$forbulkqty)
                               {
                                  $txtqtyname = 'subqty';//.$records[$pd_index]->id.'-0-0';
	                                $parent_qty = !empty($request->input($txtqtyname)[$i])?$request->input($txtqtyname)[$i]:1;
                               }

                               else{
                                  $txtqtyname = 'txt_qty'.$records[$pd_index]->id.'-0-0';
                                  
	                                $parent_qty = !empty($request->input($txtqtyname))?$request->input($txtqtyname):1;
                               } 
                              
	                            $madewith = json_decode($records[$pd_index]->madewith,true);
	                            $this_qty=[];
	                            $this_qty_unit=[];
	                            //$bulkqty = null;
	                            //$bulkqty_unit = null;
	                            foreach ($madewith as $key => $value) {
	                                list($pd_id, $sizeid, $colorid)=explode('-', $key);
	                                $p_info = $madewithresult[$pd_id ];
	                                $parent_id = $p_info->parent_id;
	                                $id_tostock = (empty($parent_id) || $parent_id==0)?$pd_id:$parent_id;
	                                $id_tostocks[] = $id_tostock;
	                                //$unit_amount =$units[$p_info->unt_id];
                                  $unit_amount = 1;
	                                $ele = abs($value);
	                                //array_push($qty,$ele);  
	                                $this_qty[$sizeid.'-'.$colorid]=abs($parent_qty*$ele);
	                                $this_qty_unit[$sizeid.'-'.$colorid]=abs($parent_qty*$ele*$unit_amount);
                                
	                                // $bulkqty['bulkqty.'.$pd_id]=$qty;
	                                // $bulkqty_unit['bulkqty.'.$pd_id]=$qty_unit;
	                                $cost=0;
	                                $total_qty = array_sum($this_qty);
	                                $record = [
	                    
	                                    $subfprimarykey => 0,
	                                    $masterfprimarykey => $newid,
	                                    'id_tostock' => $id_tostock,
	                                    'pchased_id' => !empty($request->input('pchd_id')[$i])?$request->input('pchd_id')[$i]:0,
	                                    'invd_id' => !empty($request->input('invd_id')[$i])?$request->input('invd_id')[$i]:0,
                                      'p_pdid' => !empty($request->input('subtransferto')[$i])?$request->input('subtransferto')[$i]:0,//$pd_index, /*use for store tranferTo ID*/
	                                    'pd_id' => $pd_id,
                                      'unt_id' => $p_info->unt_id,
                                      'convert_qty' => 1,
	                                    'qty' => json_encode($this_qty),
	                                    'qty_tostock' => json_encode($this_qty_unit),
	                                    'qty_total' => $total_qty,
	                                    'cost' => $cost,
                                      'qty_inhand' =>json_encode($this_qty),
                                      'qtytotal_inhand' =>$total_qty,
	                                    'add_date' => $add_date,
                                      'batch' => !empty($request->input('batch')[$i])?$request->input('batch')[$i]:'',
                                      'product_expdate' => !empty($request->input('product_expdate')[$i])?date("Y-m-d H:i:s", strtotime($request->input('product_expdate')[$i])):date("Y-m-d"),
                                      'tags' => ''
	                                
	                                ];

	                                array_push($madewithData, $record);
                                  array_push($all_madewithData,$record);
	                                
	                            }
	                            
	                            
	                    }
	                    /*@@--end*/
                    
                      if(!empty($pd_index) && strlen($records[$pd_index]->madewith)>2 && config('sysconfig.madewithstock')!='own')
                      {
                        array_push($subtableData, $madewithData);
                      }
	                    elseif(!empty($pd_index) && $records[$pd_index]->isservice=='no')
                      {
                            
		                        $pd_id=!empty($subpd_id[$i])?$subpd_id[$i]:0;
		                        $parent_id = $records[$pd_index]->parent_id;
		                        $id_tostock = (empty($parent_id) || $parent_id==0)?$pd_id:$parent_id;
		                        $id_tostocks[] = $id_tostock;
		                        
                            if($request->has('subcost')){
                              $cost = !empty($request->input('subcost')[$i])?$request->input('subcost')[$i]:0;
                            }
                            elseif($request->has('unitprice')){
                              $cost = !empty($request->input('unitprice')[$i])?$request->input('unitprice')[$i]:0;
                            }
                        
                            $transferto = !empty($request->input('subtransferto')[$i])?$request->input('subtransferto')[$i]:0;


                          /*one by one qty*/
                                
                                if(!$forbulkqty)
                                {

                                      $qty=$qtyinput[$i];
                                      $qty_unit=$qtyinput_unit[$i];
                                }

                                else{
                                    $unit_amount = !empty($request->input('convert_qty')[$i]) ? $request->input('convert_qty')[$i] :1;
                                    $qty=$bulkqty['bulkqty.'.$pd_id];
                                    $qty_unit=$bulkqty_unit['bulkqty.'.$pd_id];
                                    
                                    foreach($qty_unit as $key =>$val){
                                      $qty_unit[$key] = $val * $unit_amount;
                                    }

                                    $cost = round($cost / $unit_amount,2);
                                   
                                }
                            /**/

		                        $total_qty = array_sum($qty_unit);
		                        $record = [
		                        
		                            $subfprimarykey => 0,
		                            $masterfprimarykey => $newid,
		                            'id_tostock' => $id_tostock,
		                            'pchased_id' => !empty($request->input('pchd_id')[$i])?$request->input('pchd_id')[$i]:0,
		                            'invd_id' => !empty($request->input('invd_id')[$i])?$request->input('invd_id')[$i]:0,
                                'p_pdid' => $transferto, //$pd_index, /*use for store tranferTo ID*/
		                            'pd_id' => $pd_id,
                                'unt_id' => !empty($request->input('unt_id')[$i])?$request->input('unt_id')[$i]:0,
                                'convert_qty' => !empty($request->input('convert_qty')[$i])?$request->input('convert_qty')[$i]:0,
		                            'qty' => json_encode($qty),
		                            'qty_tostock' => json_encode($qty_unit),
		                            'qty_total' => $total_qty,
		                            'cost' => $cost??0,
                                'qty_inhand' =>json_encode($qty_unit),
                                'qtytotal_inhand' =>$total_qty,
		                            'add_date' => $add_date,
                                'batch' => !empty($request->input('batch')[$i])?$request->input('batch')[$i]:'',
                                'product_expdate' => !empty($request->input('product_expdate')[$i])?date("Y-m-d H:i:s", strtotime($request->input('product_expdate')[$i])):date("Y-m-d"),
                                'tags' => ''
		                        
		                        ];
                        
		                        array_push($subtableData, $record);
		                        if(!empty($transferto))
		                        {
                                //Sorce Product
                                $unit_s =$units[$records[$pd_index]->unt_id];
                                //Target Product
                                
                                $unt_id = $transfer_result[$transferto];
                                $unit_t =$units[$unt_id];
                              
                                //transfer and covert
                                $t_qty = $qty;
                                $t_qty_unit = [];
                                $unit_convert = $unit_s/$unit_t;
                                foreach($t_qty as $key => $val)
                                {
                                  $qty_convert = $val*$unit_convert;
                                  $t_qty[$key] = $qty_convert;
                                  $t_qty_unit[$key] = $qty_convert * $unit_t;
                                }
                                $total_qty = array_sum($t_qty);
                                $t_record = $record;
                                $t_record['pd_id'] = $transferto;
                                $t_record['qty'] = json_encode($t_qty);
                                $t_record['qty_tostock'] = json_encode($t_qty_unit);
                                $t_record['qty_total'] = $total_qty;
                                $t_record['qty_inhand'] = json_encode($t_qty);
                                $t_record['qtytotal_inhand'] =$total_qty;
                                
                                 //$transferstock = ['cost_id' => 0, 'currentstock' => $total_qty];
                                 //$record_transfer = array_except($t_record, [$subfprimarykey, $masterfprimarykey]);
		                             array_push($transfertableData, $t_record);
		                        }
		                    
	                    }
	                    /*@@/.end elseif..*/

	                }/*@@end-for*/



	            }
	            
	        }
        
        	/*endsubtable*/
          
        	return ['subtable'=>$subtableData, 'transfertable'=>$transfertableData, 'idtostock'=>$id_tostocks, 'madewith'=>$all_madewithData];


		}/**@endfun**/


		function stockProceed($data, $args, $stockModel, $multipleby=1){
                    /*
                        $multipleby = 1 => AddStock
                        $multipleby = -1 => AdjustStock
                    */
                    $wh_id = !empty($args['userinfo']['wh_id'])?$args['userinfo']['wh_id']:0;
                    $id_tostocks = $data['id_tostocks'];
                    $stock = $stockModel->select(['pd_id','pqty'])->whereIn('pd_id', $id_tostocks)->where('wh_id','=',$wh_id);
                    $stock = $stock->pluck('pqty','pd_id')->toArray();
      
                    //dd($stock);
                    $savestock=[];
                    $updatestock=[];
      
                    
                    
                    foreach ($data['subtableData'] as $ind => $row) {

                        $id_tostock = $row['id_tostock'];
                        if(array_key_exists ($id_tostock, $stock )) 
                        {
                            /*UPDATE*/ 

                            $currentStock = json_decode($stock[$id_tostock],true);
                            $currentStock = !$currentStock ?[]:$currentStock ;
                            
                            $addStock = json_decode($row['qty_tostock'], true);
                            $newStock =[];
                            foreach ($addStock as $key=>$qty) {
                              if(array_key_exists ($key, $currentStock ))
                                {
                                    $newStock[$key] = $currentStock[$key] + ($addStock[$key]*$multipleby);
                                }
                                else
                                {
                                    $newStock[$key] = $addStock[$key]*$multipleby;
                                }

                            }/*endforloop*/
                            $qty_total=array_sum($newStock); 
                            $newStock = json_encode($newStock);
                            $stock[$id_tostock] = $newStock;
                            $updatestock[$id_tostock] = ['pqty'=>$newStock, 'qty_total'=>$qty_total];
                            //dd($updatestock);
                        }
                        /*end update*/
                        else
                        {
                            /*INSERT*/
                            $qty_tostock = json_decode($row['qty_tostock'], true);
                            //dd($qty_tostock);
                            $qty_tostock = array_map(function ($x) use($multipleby) {return ($x * $multipleby);}, $qty_tostock);
                            $qty_total = array_sum($qty_tostock);
                            $qty_encode = json_encode($qty_tostock);
                            $record=[
                                'stock_id' =>0,
                                'pd_id' =>$id_tostock,
                                'pqty' => $qty_encode,
                                'qty_total'=> $qty_total,
                                'qtybyunit'=>'',
                                'wh_id' => $wh_id,
                                'upated_date' => date("Y-m-d H:i:s")
                            ];

                            array_push($savestock, $record);
                            $stock[$id_tostock] = $qty_encode;
                            
                        }
                    }
                    
                    /*
                    | Cannot insert Adjust-stock if no previose record
                    */
                    if($multipleby==1)
                    $save = $stockModel->insert($savestock);

                    foreach ($updatestock as $key => $value) {
                        
                        $update = $stockModel->where('pd_id',$key)->where('wh_id',$wh_id)->update($value);
                    }


                    /*..End...*/
    } /*../end fun..*/

  function roundnumberup($num, $increment)
  {
    return ceil($num/$increment) * $increment;
  }

  function roundnumberdown($num, $increment)
  {
    return floor($num/$increment) * $increment;
  }
  
  function afterdiscountitem($pricing, $discount)
  {
     $afterdis = 0;
    if($discount<0){
            $discount = abs($discount);
            $afterdis = $pricing - $discount;
        }
        else
        {
            $afterdis = $pricing - (($pricing*$discount)/100);
            
        }
    return $afterdis;
  }

  function totaldiscount($pricing, $qty , $discount)
  {
        $discount = (float) $discount;
        if($discount<0){
            $discount = abs($discount);
            $amount = $discount*$qty;
        }
        else
        {
            $amount = ($pricing*$discount)/100;
            $amount = $amount*$qty;
        }
      return $amount;
  }

  function discountbyitem($discount)
  {
        $discount = (float) $discount;
        if($discount<0){
            
            $discount = formatmoney(abs($discount),true);
        }
        else
        {
            
            $discount = abs($discount).config('ccms.discounttype')[1];
        }
    
        return $discount;
  }


 function calAmount($pricing, $qty ,$discount, $pvat)
    {
 		$amount=0;
 		$total =0;
        $discount = (float) $discount;
        if($discount<0){
            $discount = abs($discount);
            $amount = ($pricing - $discount)*$qty;
        }
        else
        {
            $amount = $pricing - (($pricing*$discount)/100);
            $amount = $amount*$qty;
        }
        $total = $amount;
        $pvat = (float) abs($pvat);
        $vat = 0;
        if(!empty($pvat))
        {
            $vat = (($amount*$pvat)/100);
            $total = $amount + $vat;
        }


        return [$amount, $total, $vat];

    } /*../end fun..*/

    function billsummary($subtotal,$maindiscount,$mainvat, $pay_amountusd, $pay_amountnative)
    {
      
      $roundup = (float)config('currencyinfo.roundup');
      $rounddown = (float)config('currencyinfo.rounddown');
      
      $nativesubtotal = $subtotal*config('currencyinfo.rateoutuse');
      $nativesubtotal = roundnumberup($nativesubtotal, $roundup);
      
      if($maindiscount < 0 )
          $discount = formatmoney(abs($maindiscount),true);
      elseif($maindiscount == 0)
      {
        if(config('sysconfig.df_dis')==-1)
          $discount = formatmoney(0,true);
        else
          $discount = '0'.config('ccms.discounttype')[1];
      }
      else
          $discount = abs($maindiscount).'%';
     
     
      $gtotal = calAmount($subtotal, 1 ,$maindiscount, $mainvat); //amount, total
      
      
      $total = $gtotal[0];                    
      $total_native = $total * config('currencyinfo.rateoutuse');
      $total_native = roundnumberup($total_native, $roundup);

      //
      
      $grandtotal = $gtotal[1];
      $grandtotal_native = $grandtotal * config('currencyinfo.rateoutuse');
      $grandtotal_native = roundnumberup($grandtotal_native, $roundup);
      
      $pay_amountnative_cv = 0;
      if(!empty($pay_amountnative))
      {
          $pay_amountnative_cv = $pay_amountnative/config('currencyinfo.rateoutuse'); 
      }
      $total_rec =$pay_amountusd + $pay_amountnative_cv;
      $rec_nativeconvert= $total_rec * config('currencyinfo.rateinuse');
      //
                                    
     if($total_rec>=$grandtotal)
     { 
        $change = $total_rec - $grandtotal;
       if($pay_amountusd==0)
       {
         $change_native =  $pay_amountnative - $grandtotal_native;
         if($change_native==0){
           $change = 0;
         }
       }
        elseif($change==0 && $pay_amountnative==0){
         
           $change_native = 0;
         
       }
       else
       {
          $change_native =  $change * config('currencyinfo.rateinuse');
          $change_native = roundnumberdown($change_native, $roundup);
       }
                                 
     }
     else
      {
        $change = 0;
        $change_native =0;
      }
      
      return [
        'subtotal' => $subtotal,
        'subtotal_native' => $nativesubtotal,
        'discount' => $discount,
        'total' => $total,
        'total_native' => $total_native,
        'grandtotal' => $grandtotal,
        'grandtotal_native' => $grandtotal_native,
        'rc' => $pay_amountusd,
        'rc_native' => $pay_amountnative,
        'change'  => $change,
        'change_native' => $change_native
      ];
        
    } /*../end fun..*/

    function formatMoney($amount, $format = false) 
    {
    	
    	$currencyinfo = config('currencyinfo');
    	
    	if(is_array($currencyinfo))
    	{
    		$currency = $currencyinfo['currency'];
	    	$symbol = $currencyinfo['symbol'];
	    	$decimalCount = $currencyinfo['numberdecimal'];
	    	$decimal = $currencyinfo['decimalseparator'];
	    	$thousands = $currencyinfo['thousandseparator'];
	    	$position = $currencyinfo['position'];
    	}
    	else
    	{
    		$currency = "USD";
	    	$symbol = "$";
	    	$decimalCount = 2;
	    	$decimal = ".";
	    	$thousands = ",";
	    	$position = 1;
    	}
    	



        $amount=empty($amount)?0:$amount;
        $return=number_format($amount, $decimalCount, $decimal, $thousands);
        if(!$format) return $return;

        
        switch($position) {
	      case 1:
	        return $symbol . $return;
	        break;
	      case 2:
	        return  $return . $symbol;
	        break;
	      case 3:
	        return  $symbol .' '. $return;
	        break;
	    
	    case 4:
	        return  $return . ' ' . $symbol;
	        break;

	      default:
	        return  $return;
	    }
        
        
    }

    function formatID($num){
    	$digit=!empty(config('sysconfig.iddigit'))?config('sysconfig.iddigit'):6;
    	return str_pad($num, $digit, "0", STR_PAD_LEFT);

    }

    function tofloat($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
  
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}

    function checkpermission($checkfor, $userinfo)
    {
      $return = false;
      $levelid = $userinfo['level_id'];
      $levelsetting = $userinfo['levelsetting'];
      if($levelid ==1 || in_array($checkfor, $levelsetting))
      //if(in_array($checkfor, $levelsetting))
        $return= true;
      
      return $return;
      
      
    }
    /*-.endfun*/

    function filifo($pd_id, $sizecolor, $orderqty, &$stocks, $productinfo, $filifos=[])
    {
      
      if(empty($pd_id))
       {
          return ['costdetail' => [], 'cost' => 0, 'lastorderqty' => $orderqty];
      }
      $costmethod = config('sysconfig.costmethod')??'average';
      $pcost = $productinfo[$pd_id]['pcost'];
      $xtracosts = $productinfo[$pd_id]['xtracost'];
      $xtracosts = json_decode($xtracosts, true);
      $xtrac_sizecolor = str_replace("0","",$sizecolor);
      $xtracost = $xtracosts[$xtrac_sizecolor]??0;
      
      $totalcost=0;
      $grandtotalcost = 0;
      if(empty($stocks))
      {
        $grandtotalcost = ($pcost + $xtracost) * $orderqty;
        return ['costdetail' => $filifos, 'cost' => $grandtotalcost, 'lastorderqty' => $orderqty];
      }
      
      foreach($stocks as $ind => $stock)
        {
            $avgcost = $pcost;
        
            $as_id = $stock['as_id'];
            $stock_pdid = $stock['pd_id'];
            $qtytotal_inhand = $stock['qtytotal_inhand'];
            $qty_inhand = $stock['qty_inhand'];
            $qty_inhand = json_decode($qty_inhand, true);
        
            $cost = $costmethod=='average'? $avgcost : $stock['cost'];
            $cost = $cost + $xtracost;
            $batch = $stock['batch'];
            $product_expdate = $stock['product_expdate'];
            
            
            
            $sizecolor_qty = $qty_inhand[$sizecolor]??0;
            
            
            if($pd_id==$stock_pdid && !empty($qty_inhand) && array_key_exists($sizecolor, $qty_inhand))
            {
              /*
              if($sizecolor_qty > $orderqty)
              {
                $totalcost= $orderqty*$cost;
                array_push($filifos,['as_id'=>$as_id, $sizecolor=>$orderqty, 'cost'=> $cost, 'totalcost' => $totalcost]);
                $qty_inhand[$sizecolor] = $sizecolor_qty - $orderqty;
                $stock['qty_inhand'] = json_encode($qty_inhand); 
                $stock['qtytotal_inhand'] = $qtytotal_inhand - $orderqty;
                $stocks[$ind] = $stock;
                $orderqty = 0;
              }
              elseif($sizecolor_qty>0 && $sizecolor_qty<$orderqty)
              {
                $totalcost = $sizecolor_qty*$cost;
                array_push($filifos,['as_id'=>$as_id, $sizecolor=>$sizecolor_qty, 'cost'=> $cost, 'totalcost' => $totalcost]);
                $qty_inhand[$sizecolor] = 0;
                $stock['qty_inhand'] = json_encode($qty_inhand); 
                $stock['qtytotal_inhand'] = $qtytotal_inhand - $orderqty;
                $stocks[$ind] = $stock;
                
                $orderqty = $orderqty - $sizecolor_qty;
                
              }
              */
             
              if($sizecolor_qty > 0)
              {
                if($sizecolor_qty > $orderqty)
                {
                  $pop_qty = $orderqty;
                 
                }
                else
                {
                  $pop_qty = $sizecolor_qty;
                 
                }
                $totalcost= $pop_qty*$cost;
                $qty_inhand[$sizecolor] = $sizecolor_qty - $pop_qty;
                $stock['qty_inhand'] = json_encode($qty_inhand); 
                $stock['qtytotal_inhand'] = $qtytotal_inhand - $pop_qty;
                $stocks[$ind] = $stock;
                $orderqty=$orderqty - $pop_qty;
                if($orderqty<0.04) $orderqty = 0;
                array_push($filifos,['as_id'=>$as_id, $sizecolor=>$pop_qty, 'cost'=> $cost, 'totalcost' => $totalcost, 'lastorderqty' => $orderqty, 'pd_id'=>$stock_pdid, 'product_expdate' => $product_expdate, 'batch'=>$batch]);
                           
              }
             
              $grandtotalcost+=$totalcost;
              //
              
              if($orderqty<=0)
              {
                break;
              }
              
            }
          
            
        }
      
        
        
        return ['costdetail' => $filifos, 'cost' => $grandtotalcost, 'lastorderqty' => $orderqty];
    }
    
    function sumarray($array)
    {
      $result = array_reduce($array, function($carry, $item) {
          foreach($item as $k => $v)
              $carry[$k] = isset($carry[$k]) ? $carry[$k] + $v : $v;

          return $carry;
      }, []);

      return($result);
    }

    function sumextracost($array, $extracostarray)
    {
      $grandtotalcost=0;
      foreach($array as $sizecolor => $qty)
      {
        if (!empty($extracostarray) && array_key_exists($sizecolor, $extracostarray)) {
            $extracost = abs($extracostarray[$sizecolor]);
            $amount = $extracost * $qty;
            $grandtotalcost+=$amount;
        }
      }
      return $grandtotalcost;
    }


    function beta($return = false)
    { 
      if(config('ccms.backend')=='beta') $return = true;
      return $return;
    }
    
    function dbis($db ='beta', $return = false)
    { 
      if(config('ccms.backend')==$db) $return = true;
      return $return;
    }
    
    /*
     * by phearun 20/08/2020
     * date condition query for request object
     */
    function date_validate_query($field = 'add_date', $from_date = 'fromdate', $to_date = 'todate', $opts = []){
      $appends = [];
      $querystr = '';  
      $fromdate = '';
      $todate = '';
      $query = '';
      if (request()->has($from_date) && !empty(request()->input($from_date))) 
        {
            $qry=request()->input($from_date);
            $fromdate=date("Y-m-d", strtotime($qry));
            $query="$field='".$fromdate."'";
            
            $querystr = $from_date.'='.$qry;
            $appends = [$from_date=>$qry];
        }
        if (request()->has($to_date) && !empty(request()->input($to_date))) 
        {
            $qry=request()->input($to_date);
            $todate=date("Y-m-d", strtotime($qry));
            $query="$field='".$todate."'";

            $querystr = $to_date.'='.$qry;
            $appends = [$to_date=>$qry];
        }
        if(request()->has($from_date) && request()->has($to_date) && !empty(request()->input($from_date)) && !empty(request()->input($to_date)))
        {
            $fromdate=request()->input($from_date);
            $fromdate=date("Y-m-d", strtotime($fromdate));

            $todate=request()->input($to_date);

            $todate=date("Y-m-d", strtotime($todate));
          
            $query="($field between '$fromdate' and '$todate')";
            $querystr = $from_date.'='.$fromdate.'&'.$to_date.'='.$todate;
            $appends = [$from_date=>$fromdate, $to_date=>$todate];
        }
        return [
          'from_date' => $fromdate,
          'to_date' => $todate,
          'query' => $query,
          'request_query_string' => $querystr,
          'appends' => $appends
        ];
    }

    /*
     * by phearun 20/08/2020
     * branch condition
     */
    function branch_validate_query($userinfo, $field = 'branch_id', $conjunction = ' and ', $opts = []){
        $branch_cond = $conjunction."$field=".$userinfo['branch_id'];
        if($userinfo['level_id']==1){
          //if($userinfo['branch_id']==1 || $userinfo['branch_id']==0){
          if($userinfo['branch_id']==0){
            $branch_cond = "";
          }else{
            $branch_cond = $conjunction."$field=".$userinfo['branch_id'];
          }
        }
        return $branch_cond;
    }

    /*
     * by phearun 20/08/2020
     * check blade report for loading specific customer
     */
    function get_view_by_db_name($path, $blade = 'index', $parent_path = 'backend.v', $opts = []){
      $dbname = config('ccms.backend');
      $view_path = $parent_path.$path.'.'.$dbname.'_'.$blade;
      if (!view()->exists($view_path)){
        return $parent_path.$path.'.'.$blade;
      }
      return $view_path;
    }
    
    /*
     * by phearun 27/01/2021
     * where in id|barcode filter
     */
    function where_in_filter($str, $separator = ','){
      $is_where_in = strpos($str, $separator);
      if(empty($str) || $is_where_in === false){
        return false;
      }
      $whereIn = [];
      $arr = explode($separator, $str);
      
      $whereIn = array_filter($arr, function($val){
        if(!empty($val)){
          return $val;
        }
      });
      if(empty($whereIn)){
        return;
      }
      return "'".implode("', '", $whereIn)."'";
    }
     /*
     * by phearun 27/01/2021
     * where between id|barcode filter
     */
    function where_between_filter($str, $separator = ':'){
      $is_between = strpos($str, $separator);
      if(empty($str) || $is_between === false){
        return false;
      }
      $between = explode($separator, $str);
      if(empty($between[0]) || empty($between[1])){
        $between[0]=$between[1]=0;
        return $between;
      }
//       if(!is_numeric($between[0]) || !is_numeric($between[1])){
//         if(!is_numeric($between[0]))
//           $between[0] = $between[1];
//         elseif(!is_numeric($between[1]))
//           $between[1] = $between[0];
//         else
//           $between[0] = $between[1] = 0;
//       }
      return $between;
    }
    
    /*
     * by phearun 07/04/2021
     * get lang code
     */
    function get_first_lang_code()
    {
      return config('ccms.multilang')[0][0];
    }
    /*
     * by phearun 20/05/2021
     * get current request lan for api
     */
    function get_current_request_lan(){
      return request()->input('lan') ?? get_first_lang_code();
    }

    

    function ExcelDateToUnix($dateValue = 0) {         return ($dateValue - 25569) * 86400;     }

    function cacl_aqi($avg_qty,$clow,$chight,$ilow,$ihight){
        $result = 0;
        $textcolor = '';
        if ($avg_qty <=25) {
            $result = (((50-0)/(25-0))*($avg_qty-0))+0;
            $result =  (int)$result;
            $textcolor = "#FFF";
        }
        elseif ($avg_qty <=50) {
            $result = (((100-51)/(50-26))*($avg_qty-26))+51;
            $result =  (int)$result;
            $textcolor = "#FFF";
        
        }
        else if ($avg_qty <=100) {
            $result = (((150-101)/(100-51))*($avg_qty-51))+101;
            $result =  (int)$result;
            $textcolor = "#FFF";
        
        }
        elseif ($avg_qty <=150) {
            $result  = (((200-151)/(150-101))*($avg_qty-101))+151;
            $result =  (int)$result;
            $textcolor = "#FFF";
        
        }
        elseif ($avg_qty <=250) {
          $result  = (((300-201)/(250-151))*($avg_qty-151))+201;
            $result =  (int)$result;
            $textcolor = "#FFF";
        
        }
        elseif ($avg_qty <=500) {
          $result  = (((500-300)/(500-250))*(c-250))+300;
            $result =  (int)$result;
            $textcolor = "#FFF";
        
        }

        if($result <= 50 ){
              $status = 1;
              $evaluate = "ល្អណាស់";
              $text = "<h3>ល្អណាស់</h3><p>គុណភាពខ្យល់ល្អណាស់។ សមស្របសម្រាប់សកម្មភាពខាងក្រៅផ្ទះ និងទេសចរណ៍។</p>";
              $color = "#0000FF";
        }
        elseif ($result <=100 ){
              $status = 2;
              $evaluate = "ល្អ";
              $text = "<h3>ល្អ</h3><p>គុណភាពខ្យល់ល្អ។ អាចធ្វើសកម្មភាពទៅខាងក្រៅ និងការធ្វើដំណើរដូចធម្មតា។ បុគ្គលដែលមានប្រតិកម្មអាលែហ្សីតិចតួចគួរតែកាត់បន្ថយពេលវេលាសម្រាប់សកម្មភាពខាងក្រៅ។</p>";
              $color = "#008000";
        }elseif ($result <=150 ){
              $status = 3;
              $evaluate = "ធម្យម(ការបំពុលកម្រិតស្រាល)";
              $text = "<h3>ធម្យម(ការបំពុលកម្រិតស្រាល)</h3><p><strong>មនុស្សទូទៅ៖</strong> អាចធ្វើសកម្មភាពក្រៅផ្ទះធម្មតា។</p><p><strong>អ្នកដែលមានសុខភាពទន់ខ្សោយ៖</strong> ប្រសិនបើមានរោគសញ្ញាដំបូងដូចជាការក្អក ការដកដង្ហើមពិបាក រលាកភ្នែកនោះ សូមកាត់បន្ថយរយៈពេលនៃសកម្មភាពខាងក្រៅរបស់អ្នក។</p>";
              $color = "#c4c400";
        
        }elseif ($result <=200 ){
              $status = 4;
              $evaluate = "បង្គួរ(ការបំពុលមធ្យម)";
              $text = "<h3>បង្គួរ(ការបំពុលមធ្យម)</h3><p><strong>មនុស្សទូទៅ៖</strong>គួរតែតាមដានសុខភាពប្រសិនបើមានរោគសញ្ញាដំបូងដូចជាការក្អក ការពិបាកដកដង្ហើម រលាកភ្នែក សូមកាត់បន្ថយរយៈពេលនៃសកម្មភាពខាងក្រៅរបស់អ្នក ឬប្រើប្រាស់ឧបករណ៍ការពារផ្លូវដង្ហើមប្រសិនបើចាំបាច់។</p><p><strong>អ្នកដែលមានសុខភាពទន់ខ្សោយ៖</strong> គួរកាត់បន្ថយរយៈពេលនៃសកម្មភាពខាងក្រៅរបស់អ្នក។ ឬ ប្រើប្រាស់ឧបករណ៍ការពារផ្លូវដង្ហើមប្រសិនបើចាំបាច់។ ប្រសិនបើមានស្ថានភាពសុខភាពដូចជាការក្អក ការពិបាកដកដង្ហើម ឈឺទ្រូង ឈឺក្បាល ចង្វាក់បេះដូងលោតខុសប្រក្រតី ក្អួតចង្អោរ អស់កម្លាំង នោះគួរតែពិគ្រោះជាមួយវេជ្ជបណ្ឌិត។</p>";
              $color = "#FFA500";
        
        }elseif ($result <=300 ){
              $status = 5;
              $evaluate = "ខ្ពស់(ការបំពុលខ្លាំង)";
              $text = "<h3>ខ្ពស់(ការបំពុលខ្លាំង)</h3><p>មនុស្សគ្រប់គ្នាគួរតែជៀសវាងសកម្មភាពខាងក្រៅ; ជៀសវាងតំបន់ដែលមានការបំពុលខ្យល់ខ្ពស់ ឬប្រើប្រាស់ឧបករណ៍ការពារផ្លូវដង្ហើមប្រសិនបើចាំបាច់។ ប្រសិនបើមានរោគសញ្ញាសុខភាពនោះ គួរតែពិគ្រោះជាមួយវេជ្ជបណ្ឌិត។</p>";
              $color = "#FF0000";
        
        }else{
              $status = 6;
              $evaluate = "ខ្ពស់ខ្លាំង (ការបំពុលធ្ងន់ធ្ងរ)";
              $text = "<h3>ខ្ពស់ខ្លាំង (ការបំពុលធ្ងន់ធ្ងរ)</h3><p>ការព្រមានអំពីស្ថានភាពអាសន្នចំពោះសុខភាព។ សូម្បីតែមនុស្សដែលមានសុខភាពល្អនឹងពិបាកការស៊ូទ្រាំក្នុងកំឡុងពេលនេះ។ វាអាចមានការរលាកខ្លាំង និងរោគសញ្ញាជាច្រើនទៀត។ ដូច្នេះមនុស្សទាំងអស់គួរតែចៀសវាងសកម្មភាពចេញទៅខាងក្រៅ។</p>";
              $color = "#800080";
        }
        
           
        return [
          'qty' => $result,
          'status' => $status,
          'evaluate' => $evaluate,
          'text'   => $text,
          'color'  => $color
        ];
    }
  
?>