<?php

namespace App\Models\CustomerSheet;

use App\Models\CustomerSheet\CustomerSheetAttachment;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;

class CustomerSheet extends Model
{
    use HasFactory, SoftDeletes, LogActivityTrait;

    protected $table = 'customer_sheets';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'customer_phone', 'order_date', 'order_type', 'filling', 'amount_charged', 'order_status', 'communication', 'updated_by', 'project_assigned', 'attachment', 'status'];

    protected static function getLogEvents()
    {
        /** Events to be logged */
        return [
            'created',
            'updated',
            'deleted',
        ];
    }


    public function shouldBeLogged()
    {
        return true;
    }

    /**
     * @var mixed
     */
    protected static function boot()
    {
        parent::boot();
        self::bootLogActivity();
    }
    public function attachments()
    {
        return $this->hasMany(CustomerSheetAttachment::class);
    }

    /**
     * Get the actor (user or admin) associated with the log action.
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator', 'creator_type', 'creator_id');
    }
    /**
     * Scope a query to only include records created by the authenticated user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthUserRecords($query)
    {
        return $query->where(function ($query) {
            $query->where('creator_id', Auth::id())
                ->where('creator_type', Auth::user()->getMorphClass());
        });
    }

    /**
     * Scope a query to include records created by clients.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientRecords($query)
    {
        return $query->where('creator_type', 'App\Models\Client');
    }
    /**
     * Scope a query to only include records created by the authenticated user Or Clients.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthOrClientRecords($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('creator_id', Auth::id())
                    ->where('creator_type', Auth::user()->getMorphClass());
            })->orWhere(function ($query) {
                $query->where('creator_type', 'App\Models\Client');
            });
        });
    }

}

