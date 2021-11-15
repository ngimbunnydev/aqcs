<?php
	function cmb_listing($arr_list,$arr_select,$disable,$disablestatus,$by_key=[]){
		//key store as value and value as label of combo
		$stm='';
		$selected="";
        if(empty($arr_list))return $stm;
        $count_select=count($arr_select);
		foreach($arr_list as $key=>$value){
      if(!empty($by_key)){
        $key = $value[$by_key[0]];
        $value = $value[$by_key[1]];
      }
			for($i=0;$i<$count_select;$i++){
				if(strcasecmp(trim($arr_select[$i]),trim((string)$key))==0){
				/*if(trim((string)$arr_select[$i])==trim((string)$key)){	*/
					$selected="selected='selected'";
					break;
				}
				else{ $selected="";}
			}//end for
			
			/*check for disable*/
			$disable_value="";
			if($disable==$key){
				$disable_value="disabled='disabled'";
			}
			 $stm.="<option value='$key' $selected $disable_value>".$value."</option>";
		}//end for each	
		return $stm;
	}///end function

	/********************************** CHECKBOX ******************************/
	function check_select($chk_name,$arr_chk,$chked,$js_fun){
			//key store as label and value as value of checkbox
			$stm='';
	        if(empty($arr_chk))return $stm;
			$i=0;
			foreach($arr_chk as $label=>$value){
				if($label=="<br>"){
					$stm.= "<br /><br class='line-height-10' />";
				}
				else{
				    
                    /*echo "<label><input name='".$chk_name."' type='radio' value='".$value."' ".$js_fun.""; if(strtolower($chked)==strtolower($value)){ echo "checked='checked'";}  echo " />".$label."</label> ";*/    
                    $stm.= "<label class='radio-inline frm-label'>";
					if($value<>(string)$chked){  
						
						if($i==0){       //when the value of u 12 select='' must set the slect to the first value
							$stm.= "<input name='".$chk_name."' id='".$chk_name."' type='radio' value='".$value."' ".$js_fun." checked='checked' />";
							
						}
						else{$stm.= "<input name='".$chk_name."' id='".$chk_name."' type='radio' value='".$value."' ".$js_fun." />";}			                 
					}
					else{
						$stm.= "<input name='".$chk_name."' id='".$chk_name."' type='radio' value='".$value."' ".$js_fun." checked='checked' />";
					}//end if	
                    
					$stm.= $label; //for label
					$stm.= "</label>";
                    
				}
				$i+=1;			                                     
			}//end for
			return $stm;
		}/**@endfun**/


		/**
	     * @param Array elements[id,parent_id,name,...]
	     * @param Int parentId
	     * @return String
	     */
	    function CategoryCheckboxTree(array $elements,$tree = "",$chk_name="category",$select=[]) {
	    	$tree.= "<ul style='list-style: none;'>";
	        foreach($elements as $element)
	        {
	        	$checked="";
	        	if (in_array($element['c_id'], $select))$checked="checked='checked'";

	        	$list= "<label><input name='".$chk_name."' id='".$chk_name."' type='checkbox' value='".$element['c_id']."' ".$checked."/>&nbsp;".$element['title'] ."</label>";

	            if (!empty($element['children'])) {
	                $tree.= "<li>";
	                $tree.= $list;
	                $tree=	CategoryCheckboxTree($element['children'],$tree,$chk_name,$select);
	                $tree.= "</li>";
	            } else {
	                $tree.= "<li>" . $list . "</li>";
	            }
	        }
	        $tree.= "</ul>";

	        return $tree;
	    }/**@endfun**/


	    /**
	     * @param Array elements[id,parent_id,name,...]
	     * @param Int parentId
	     * @return String
	     */
	    function CategorySelectboxTree(array $elements,$space="", $tree = "",$select=[], $id='c_id') {
	    	$level = ":....";
	        foreach($elements as $element)
	        {
	        	$checked="";
	        	if (in_array($element[$id], $select))$checked="selected='selected'";

	        	$list= $element['title'];

	        	$tree.= "<option value='".$element[$id]."' ".$checked.">";
	                $tree.= $space.$level.$list;
	                $tree.= "</option>";

	            if (!empty($element['children'])) {
	            	$space_sub = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $space;
	                $tree=	CategorySelectboxTree($element['children'],$space_sub,$tree,$select,$id);
	            }
	        }
	

	        return $tree;
	    }/**@endfun**/

      /**
	     * @param Array elements[id,parent_id,name,...]
	     * @param Int parentId
	     * @return String
	     */
	    function ApiSelectboxTree(array $elements,$space="", $tree = [], $id='c_id') {
	    	$level = ":....";
	        foreach($elements as $element)
	        {
	        	
	        	$list= $element['title'];
            $tree[$element[$id]] = $space.$level.$list;
	        	

	            if (!empty($element['children'])) {
	            	$space_sub = html_entity_decode("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $space);
	                $tree=	ApiSelectboxTree($element['children'],$space_sub,$tree,$id);
	            }
	        }
	

	        return $tree;
	    }/**@endfun**/


	    /**
	     * @param Array elements[id,parent_id,name,...]
	     * @param Int parentId
	     * @return String
	     */
	    function NestableLists(array $elements,$tree = "") {
	    	$tree.= "<ol class='dd-list'>";
	        foreach($elements as $element)
	        {

	        	$list= "<div class='dd-handle'>".$element['title']."</div>";

	            if (!empty($element['children'])) {
	                $tree.= "<li class='dd-item' id='no-drag' data-id='".$element['c_id']."'>";
	                $tree.= $list;
	                $tree=	NestableLists($element['children'],$tree);
	                $tree.= "</li>";
	            } else {
	                $tree.= "<li class='dd-item' id='no-drag' data-id='".$element['c_id']."'>" . $list . "</li>";
	            }
	        }
	        $tree.= "</ol>";

	        return $tree;
	    }/**@endfun**/


	    function orderMenu(array $args, array $querystr, array $perpag, $obj_info)
	    {
	    	#$caption,$sort,$current_sort,$mdefault,$method,$act,$linkstyle#
			$order_show="";
			$querystr = array_merge($querystr, $perpag);
			if($args['sort']==$args['current_sort'])
			{ //for make current
					if($args['method']=="asc"){
						$url=url_builder($obj_info['routing'],
										[$obj_info['name'],$args['act']],
										array_merge(['sort='.$args['sort'], 'order'=>'desc'], $querystr)
									);
						$order_show.= '<span class=""><a href="'.$url.'">';
						$order_show.=$args['caption'];
						$order_show.= '';
                		$order_show.= '</a> <i class="fa fa-sort-up" aria-hidden="true"></i></span>';
					}//end if method
					else{
						$url=url_builder($obj_info['routing'],
										[$obj_info['name'],$args['act']],
										array_merge(['sort='.$args['sort'], 'order'=>'asc'], $querystr)
									);
						$order_show.= '<span class=""><a href="'.$url.'">';
						$order_show.=$args['caption'];
						$order_show.= '';
                		$order_show.= '</a> <i class="fa fa-sort-down" aria-hidden="true"></i></span>';
					}
					
			}//end  if $name
			else
			{
				$url=url_builder(	$obj_info['routing'],
									[$obj_info['name'],$args['act']],
									array_merge(['sort='.$args['sort'], 'order'=>$args['mdefault']], $querystr)
								);
						$order_show.= '<span class=""><a href="'.$url.'">';
						$order_show.=$args['caption'];
                		$order_show.= '</a></span>';
			}     
			return $order_show;      
			
	}/**@endfun**/


	function checkbox_select($chk_name,$arr_chk,$arr_chked,$js_fun){
			//key store as label and value as value of checkbox
		$stm = '';
		$check='';
			foreach($arr_chk as $item){
				$key	= $item['title'];
				$value = $item['id'];
				for($j=0;$j<count($arr_chked);$j++){
					$chked=trim($arr_chked[$j]);
					if(trim($value)==$chked){
						$check="checked='checked'";
						break;
					}
					else{$check="";}
				}//end nested for       
				$stm .= "<label class='checkbox-inline' style='margin:0px 0px 0px 0px;'><input name='".$chk_name."' id='".$chk_name."' type='checkbox' value='".$value."' ".$js_fun." $check />".$key."</label><br class='line-height-10' />";    
			}//end for

		return $stm;

	}///end function

?>