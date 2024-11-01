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

class ThirdPartyRoleModel extends Model
{
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'third_party_roles';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['team_key','invoice_id','client_id','description','merchant_type','transaction_id','payment_status','status'];

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
    public function creator(): MorphTo
    {
        return $this->morphTo('creator', 'creator_type', 'creator_id');
    }
    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_key');
    }
    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
    }
    public function getClient()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }



    /** Scopes Start */
    /** Stats Dashbaord Start */

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
                return $query->whereMonth('created_at', $monthNumeric + 1);
            }
        }
        return $query;
    }


    public function scopeApplyYear(Builder $query, $year)
    {
        if ($year && $year > 0) {
            return $query->whereYear('created_at', $year);
        }
        return $query;
    }
    /** Stats Dashbaord End */
    /** Scopes End */
}
