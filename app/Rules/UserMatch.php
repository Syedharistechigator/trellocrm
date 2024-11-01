<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\BoardListCardAttachment;

class UserMatch implements Rule
{
    protected $userId;
    protected $model;
    protected $fieldName;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $model, $userId)
    {
        $this->model = $model;
        $this->userId = $userId;
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
        $model = $this->model::find($value);
        return $model && $model->user_id == $this->userId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The selected {$this->fieldName} is invalid for the authenticated user.";
    }
}
