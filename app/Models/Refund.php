<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'refunds';
    protected $primaryKey = 'id';
    protected $guarded = [];

    /** Scopes Start*/
    public function scopeYearlyRefund($query)
    {
        return $query->where(['type' => 'refund', 'qa_approval' => 1])->whereYear('created_at', now()->format('Y'));
    }
    public function scopeYearlyChargeback($query)
    {
        return $query->where(['type' => 'chargeback', 'qa_approval' => 1])->whereYear('created_at', now()->format('Y'));
    }
    /** Scopes End*/

    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_key');
    }

    public function getClientName(){
        return $this->belongsTo(Client::class,'client_id','id')->select('name')->withDefault(['name'=>'---']);
    }

    public function getClientEmail(){
        return $this->belongsTo(Client::class,'client_id','id')->select('email')->withDefault(['email'=>'---']);
    }
}
