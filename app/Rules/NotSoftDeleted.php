<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotSoftDeleted implements Rule
{
    protected $model;
    protected $fieldName;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->fieldName = str_replace('_', ' ', $attribute);
        return $this->model::withTrashed()->where('id', $value)->whereNull('deleted_at')->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The selected {$this->fieldName} is invalid or has been deleted.";
    }
}
