<?php

namespace App\Models\Team;

use App\Models\Team;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FixedCostingModel extends Model
{
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'team_fixed_costings';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['team_key','amount','date','status'];

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
    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
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
                return $query->where('month', $monthNumeric + 1);
            }
        }
        return $query;
    }


    public function scopeApplyYear(Builder $query, $year)
    {
        if ($year && $year > 0) {
            return $query->where('year', $year);
        }
        return $query;
    }
    /** Stats Dashbaord End */
    /** Scopes End */
}
