<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spending extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spendings';
    protected $primaryKey = 'id';
    protected $guarded = [];

    /** Scopes Start*/
    public function scopeBrandMonthSpending($query, $brandKey)
    {
        return $query->where('brand_key', $brandKey)->whereMonth('created_at', now()->month);
    }
    public function scopeYearlySpending($query)
    {
        return $query->whereYear('created_at', now()->format('Y'));
    }

    public function scopeMonthlySpending($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopePlatformSpending($query, $platform)
    {
        return $query->where('platform', $platform)->whereMonth('created_at', now()->month);
    }
    /** Scopes End*/
}
