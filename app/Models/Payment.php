<?php

namespace App\Models;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $guarded = [];

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
//            'created',
//            'updated',
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
        return $this->morphTo('actor', 'actor_type', 'actor_id')->withTrashed();
    }

    public function getAuthorizeMerchant()
    {
        return $this->belongsTo(PaymentMethod::class, 'merchant_id', 'id');
    }

    public function getExpigateMerchant()
    {
        return $this->belongsTo(PaymentMethodExpigate::class, 'merchant_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'id');
    }

    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_key');
    }

    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
    }

    public function getTeamName()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key')->select('name')->withDefault(['name' => '---']);
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key');
    }

    public function getBrandName()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key')->select('name')->withDefault(['name' => '---']);
    }

    public function getAgentName()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id')->select('name')->where('status', 1)->withDefault(['name' => '']);
    }
    public function getProjectName()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->select('name')->where('status', 1)->withDefault(['name' => '']);
    }

    public function clientcards()
    {
        return $this->belongsTo(CcInfo::class, 'clientid', 'client_id');
    }

    /** Scopes Start*/
    public function scopeBrandSuccessPayments($query, $brandKey)
    {
        return $query->where(['brand_key' => $brandKey, 'payment_status' => 1]);
    }

    public function scopeMonthSuccessPayments($query, $team_key = null, $agent_id = null)
    {
        $query->where('payment_status', 1)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);

        if ($team_key !== null) {
            $query->where('team_key', $team_key);
        }

        if ($agent_id !== null) {
            $query->where('agent_id', $agent_id);
        }

        return $query;
    }

    public function scopeTodaySuccessPayments($query, $team_key = null, $agent_id = null)
    {
        $query->where('payment_status', 1)
            ->whereDate('created_at', today());

        if ($team_key !== null) {
            $query->where('team_key', $team_key);
        }

        if ($agent_id !== null) {
            $query->where('agent_id', $agent_id);
        }

        return $query;
    }

    public function scopeFreshSuccessPayments($query, $team_key = null, $agent_id = null)
    {
        $query->where('payment_status', 1)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
            ->where(function ($query) {
                $query->where('sales_type', 'Fresh')
                    ->orWhere('sales_type', 'New');
            });

        if ($team_key !== null) {
            $query->where('team_key', $team_key);
        }

        if ($agent_id !== null) {
            $query->where('agent_id', $agent_id);
        }

        return $query;
    }

    public function scopeUpsaleSuccessPayments($query, $team_key = null, $agent_id = null)
    {
        $query->where(['payment_status' => 1, 'sales_type' => 'Upsale'])
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),]);
        if ($team_key !== null) {
            $query->where('team_key', $team_key);
        }

        if ($agent_id !== null) {
            $query->where('agent_id', $agent_id);
        }

        return $query;
    }

    public function scopeYearlySuccessIncome($query)
    {
        return $query->where('payment_status', 1)->whereYear('created_at', now()->format('Y'));
    }

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


    public function scopeApplyDate(Builder $query, $date)
    {
        if ($date && $date > 0) {
            return $query->whereDate('created_at', $date);
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

    public function scopeApplyPaymentStatusProcessTime($query, $month, $year)
    {
        if ($month) {
            $input_month = Str::title($month);
            $monthNumeric = array_search($input_month, config('app.months'));
            if ($monthNumeric !== false) {
                $query->whereMonth('payment_status_process_time', $monthNumeric + 1);
            }
        }
        if ($year && $year > 0) {
            $query->whereYear('payment_status_process_time', $year);
        }
        return $query;
    }

    /** Stats Dashbaord End */

    /** Scopes End*/

}
