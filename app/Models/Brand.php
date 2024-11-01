<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Team;
use PharIo\Manifest\Email;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $fillable = ['brand_key', 'name', 'brand_url', 'logo', 'status', 'merchant_id','is_paypal','is_amazon','expigate_id','payarc_id','default_merchant_id','crawl','checkout_version', 'smtp_host', 'smtp_email', 'smtp_password', 'smtp_port','admin_email','phone','phone_secondary','email','email_href','contact_email','contact_email_href','website_name','website_logo','address','chat',];
    protected $dates = ['deleted_at'];

    /** Pre Define Attributes*/
    public function getLastMonthAmountAttribute()
    {
        return thousand_format((int)Payment::brandSuccessPayments($this->brand_key)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth(),])
            ->sum('amount'));
    }
    public function getCurrentMonthAmountAttribute()
    {
        return thousand_format((int)Payment::brandSuccessPayments($this->brand_key)->monthSuccessPayments()->sum('amount'));
    }
    public function getCurrentMonthSpendingAttribute()
    {
        return thousand_format((int)Spending::brandMonthSpending($this->brand_key)->sum('amount'));
    }
    public function getTotalSpendingAttribute()
    {
        return thousand_format((int)Spending::where('brand_key',$this->brand_key)->sum('amount'));
    }
    /** End Pre Define Attributes*/

    public function getTeams()
    {
        return $this->hasManyThrough(Team::class, AssignBrand::class, 'brand_key', 'team_key', 'brand_key', 'team_key');
    }

    public function getMerchant()
    {
        return $this->belongsTo(PaymentMethod::class,'merchant_id', 'id');
    }
    public function getMerchantExpigate()
    {
        return $this->belongsTo(PaymentMethodExpigate::class,'expigate_id', 'id');
    }
    public function getProjects()
    {
        return $this->hasMany(Project::class,'brand_key', 'brand_key');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'brand_key', 'brand_key');
    }

    public function spendings()
    {
        return $this->hasMany(Spending::class, 'brand_key', 'brand_key');
    }

    public function getBrandEmails()
    {
        return $this->hasMany(EmailConfiguration::class, 'brand_key', 'brand_key');
    }
}
