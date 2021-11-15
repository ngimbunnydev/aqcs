<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Backend\Product;

class Issubunit implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    protected $parent_unit;

    public function __construct($parent_unit)
    {
        $this->parent_unit = $parent_unit;
    }


    public function passes($attribute, $value)
    {
        if ($this->parent_unit==$value) return true;
        else
        {
            $parent =  get_parent("cms_unit","unt_id","parent_id",$value,"");
            if(!empty($parent)){
                $parent = explode(',', $parent);
                if(array_search($this->parent_unit, $parent)>=0) return true;
                else return false;

            }
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('ccms.invlidunitgroup');
    }
}