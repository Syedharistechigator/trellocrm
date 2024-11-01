<?php

namespace App\Models\CustomerSheet;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CustomerSheetAttachment extends Model
{
    use HasFactory, SoftDeletes, LogActivityTrait;

    protected $table = 'customer_sheet_attachments';
    protected $primaryKey = 'id';
    protected $fillable = ['creator_id', 'creator_type', 'customer_sheet_id', 'original_name', 'file_name', 'file_path', 'base_encode', 'file_size', 'mime_type', 'extension', 'status'];

    public function fill(array $attributes)
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }
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

    public function customer_sheets()
    {
        return $this->belongsTo(CustomerSheet::class);
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
