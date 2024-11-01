<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class   LogAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_actions';
    protected $primaryKey = 'id';
    protected $fillable = ['previous_record','actor_id','actor_type','loggable_id','loggable_type','action'];

    /**
     * Get the actor (user or admin) associated with the log action.
     */
    public function actor(): MorphTo
    {
        return $this->morphTo('actor', 'actor_type', 'actor_id')->withTrashed();
    }
    /**
     * Get the entity (e.g., User, Admin , Client) associated with the log action.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo('loggable', 'loggable_type', 'loggable_id')->withTrashed();
    }
}
