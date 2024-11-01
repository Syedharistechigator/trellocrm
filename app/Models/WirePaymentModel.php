<?php

namespace App\Models;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WirePaymentModel extends Model
{
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'wire_payments';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['team_key', 'brand_key', 'actor_id', 'actor_type', 'agent_id', 'client_name', 'client_email', 'client_phone', 'project_title', 'project_description', 'due_date', 'transaction_id', 'screenshot', 'payment_approval', 'approval_updated_by', 'approval_actor_type', 'status',];

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

    /**
     * Get the actor (user or admin) associated with the log action.
     */
    public function actor(): MorphTo
    {
        return $this->morphTo('actor', 'actor_type', 'actor_id');
    }

    /**
     * Get the actor (user or admin) associated with the log action.
     */
    public function approval_actor(): MorphTo
    {
        return $this->morphTo('approval_actor', 'approval_actor_type', 'approval_updated_by');
    }

    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key');
    }

    public function getAgent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }

    /** Scopes Start */

    public function scopeApplyTeamKey(Builder $query, $teamKey)
    {
        if ($teamKey && $teamKey > 0) {
            return $query->where('team_key', $teamKey);
        }
        return $query;
    }

    public function scopeApplyMonth(Builder $query, $month)
    {
        if ($month) {
            $input_month = Str::title($month);
            $monthNumeric = array_search($input_month, config('app.months'));
            if ($monthNumeric !== false) {
                return $query->whereMonth('due_date', $monthNumeric + 1);
            }
        }
        return $query;
    }

    public function scopeApplyDate(Builder $query, $date)
    {
        if ($date && $date > 0) {
            return $query->whereDate('due_date', $date);
        }
        return $query;
    }

    public function scopeApplyYear(Builder $query, $year)
    {
        if ($year && $year > 0) {
            return $query->whereYear('due_date', $year);
        }
        return $query;
    }

    public function scopeApplyCreatedAt($query, $month, $year)
    {
        if ($month) {
            $input_month = Str::title($month);
            $monthNumeric = array_search($input_month, config('app.months'));
            if ($monthNumeric !== false) {
                $query->whereMonth('due_date', $monthNumeric + 1);
            }
        }
        if ($year && $year > 0) {
            $query->whereYear('due_date', $year);
        }
        return $query;
    }

    /** Stats Dashbaord End */

    /** Scopes End*/
}
