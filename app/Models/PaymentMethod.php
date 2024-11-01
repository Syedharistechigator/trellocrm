<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public static function isCapacityAvailable($MerchantId, $totalAmount): bool
    {
        $paymentMethod = self::where('id', $MerchantId)->where('status', 1)->first();

        if ($paymentMethod) {
            return $paymentMethod->capacity >= ($paymentMethod->cap_usage + $totalAmount);
        }
        return false;
    }

    public static function isAuthorizeAvailable($MerchantId): bool
    {
        return self::where('id', $MerchantId)->where('authorization', 1)->exists();
    }

    public static function isCapacityAndAuthorizeAvailable($MerchantId, $totalAmount): bool
    {
        $paymentMethod = self::where('id', $MerchantId)
            ->where('status', 1)
            ->where('authorization', 1)
            ->first();

        return $paymentMethod ? $paymentMethod->capacity >= ($paymentMethod->cap_usage + $totalAmount) : false;

    }

}
