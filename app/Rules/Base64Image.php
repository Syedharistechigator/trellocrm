<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Intervention\Image\Facades\Image as InterventionImage;

class Base64Image implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
            // Remove the "data:image/{type};base64," from the base64 string
            $base64 = substr($value, strpos($value, ',') + 1);
            // Decode the base64 string
            $image = base64_decode($base64);

            return InterventionImage::make($image) !== false;
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
        return 'The :attribute must be a valid base64-encoded image.';
    }
}
