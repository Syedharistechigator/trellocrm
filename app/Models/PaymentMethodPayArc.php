<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PaymentMethodPayArc extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'payment_method_payarcs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getBrands()
    {
        return $this->hasMany(Brand::class, 'payarc_id', 'id');
    }
}
