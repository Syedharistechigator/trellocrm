<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($invoice) {
            $invoice->splitPayments()->delete();
        });
    }
    /** Relations */
    public function splitPayments()
    {
        return $this->hasMany(SplitPayment::class, 'invoice_id','invoice_key');
    }
    public function getPaymentsTransactionLogs()
    {
        return $this->hasMany(PaymentTransactionsLog::class, 'invoiceid','invoice_key');
    }
    public function getPayments()
    {
        return $this->hasMany(Payment::class, 'invoice_id','invoice_key');
    }
    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_key','brand_key')->withTrashed();
    }
    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key','team_key')->withTrashed();
    }
    public function getBrandWithOutTrashed(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->withoutTrashed();
    }
    public function getBrandName()
    {
        return $this->belongsTo(Brand::class, 'brand_key','brand_key')->select('name')->withDefault(['name'=>'---']);
    }
    public function getBrandUrl()
    {
        return $this->belongsTo(Brand::class, 'brand_key','brand_key')->select('brand_url')->withDefault(['brand_url'=>'---']);
    }
    public function getClient(){
        return $this->belongsTo(Client::class,'clientid','id');
    }
    public function getClientName(){
        return $this->belongsTo(Client::class,'clientid','id')->select('name')->withDefault(['name'=>'---']);
    }
    public function getAgent(){
        return $this->belongsTo(User::class,'agent_id','id');
    }
    public function getAgentName(){
        return $this->belongsTo(User::class,'agent_id','id')->select('name')->where('status',1)->withDefault(['name'=>'---']);
    }
    public function getClientCcInfo(){
        return $this->hasMany(CcInfo::class,'client_id','clientid');
    }
    public function signatures(){
        return $this->hasMany(InvoiceSignature::class,'invoice_id','invoice_key');
    }
    public function getProject(){
        return $this->belongsTo(Project::class,'project_id','id');
    }
    public function getProjectTitle()
    {
        return $this->belongsTo(Project::class, 'project_id','id')->select('project_title')->withDefault(['project_title'=>'']);
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

