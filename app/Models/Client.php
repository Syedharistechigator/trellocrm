<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class Client extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $guarded = [];

    // Define a one-to-many relationship with ccInfos
    public function ccInfos()
    {
        return $this->hasMany(CcInfo::class, 'client_id', 'id');
    }

    // Define a one-to-many relationship with payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'clientid', 'id');
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'clientid', 'id');
    }

    public function getBrandName()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key')->select('name')->withDefault(['name' => '']);
    }

    public function getBrandLogo()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key')->select('logo')->withDefault(['logo' => '']);
    }

    public function getCustomerPaymentProfile()
    {
        return $this->hasMany(client::class, 'client_id', 'id');
    }

    public function getThirdPartyRole()
    {
        return $this->belongsTo(client::class, 'client_id', 'id');
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
    /** Stats Dashbaord End */

    /** Scopes End*/
}
