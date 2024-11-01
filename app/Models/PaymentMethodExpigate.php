<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PaymentMethodExpigate extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'payment_method_expigates';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getBrands(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Brand::class,'expigate_id','id');
    }
    public static function isCapacityAvailable($MerchantId, $totalAmount): bool
    {
        $paymentMethodExpigate = self::where('id', $MerchantId)->where('status', 1)->first();

        if ($paymentMethodExpigate) {
            return $paymentMethodExpigate->capacity >= ($paymentMethodExpigate->cap_usage + $totalAmount);
        }
        return false;
    }
}
