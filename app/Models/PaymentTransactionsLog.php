<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransactionsLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_transactions_logs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getAuthorizeMerchant()
    {
        return $this->belongsTo(PaymentMethod::class, 'merchant_id', 'id');
    }
    public function getExpigateMerchant()
    {
        return $this->belongsTo(PaymentMethodExpigate::class, 'merchant_id', 'id');
    }
    public function getPayArcMerchant()
    {
        return $this->belongsTo(PaymentMethodPayArc::class, 'merchant_id', 'id');
    }
    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'invoice_key')->withTrashed();
    }
    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key')->withTrashed();
    }
    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_key', 'brand_key')->withTrashed();
    }
    public function getClient()
    {
        return $this->belongsTo(Client::class, 'clientid', 'id')->withTrashed();
    }
}
