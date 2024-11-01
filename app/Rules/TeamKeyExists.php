<?php

namespace App\Rules;

use App\Models\Team;
use Illuminate\Contracts\Validation\Rule;

class TeamKeyExists implements Rule
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
        if (in_array(0, $value)) {
            /** "0" (For All Teams) is allowed, so remove it for validation */
            $value = array_diff($value, [0]);
        }
        $existingTeamKeys = Team::pluck('team_key')->toArray();
        foreach ($value as $teamKey) {
            if (!in_array($teamKey, $existingTeamKeys)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'One or more selected Team keys do not exist.';
    }
}
