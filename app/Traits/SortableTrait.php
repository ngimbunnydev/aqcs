<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SortableTrait {
    /**
     * by Phearun
     * @Date: 27/02/2021
     * Single field sortable
     */
    public function sortable($fields = [], $default=null) {
      $default_fields = [ 'id', 'title', 'add_date'];
      $fields = (is_array($fields) && !empty($fields)) ? $fields : $this->sortable_fileds;
      $default = (!is_null($default)) ? $default : $this->default_sortfield;
      
      $sort_fileds = array_merge($default_fields, $fields);
      
      $sort = in_array(request()->input('sort'), $sort_fileds) ? request()->input('sort') : $default;
      $order = request()->input('order') === 'asc' ? 'asc' : 'desc';
      return [$sort, $order];
    }

}