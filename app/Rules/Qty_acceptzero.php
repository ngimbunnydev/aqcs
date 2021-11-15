<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Backend\Product;

class Qty_acceptzero implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    protected $parent_unit;

    public function passes($attribute, $value)
    {
        $count = count($value);
        for($i=0; $i<$count; $i++)
        {
            if((float)$value[$i]>=0) return true;
        }
        return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('ccms.fieldreqire');
    }
}