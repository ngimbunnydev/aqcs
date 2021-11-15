<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Backend\Product;

class Notin_array implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    protected $arrays;

    public function __construct($arrays)
    {
        $this->arrays = $arrays;
    }


    public function passes($attribute, $value)
    {
        return !in_array($value, $this->arrays);

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('ccms.cannotmadewith');
    }
}