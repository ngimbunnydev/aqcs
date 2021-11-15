<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Backend\Product;

class Isparent implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //return $value == 2;
                $parent_id = $value;
                $chkparent = Product::select('parent_id')->where('pd_id',$parent_id)->where('isservice','no')->whereRaw('length(madewith)<=2')->get(['parent_id'])->toArray();
                if(!empty($chkparent))
                $chkparent = $chkparent[0]['parent_id'];
                else
                $chkparent ='cannotbeparent';
                return empty($chkparent);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('ccms.invlidgroup');
    }
}