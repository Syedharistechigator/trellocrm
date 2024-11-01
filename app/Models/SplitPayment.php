<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SplitPayment extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'split_payments';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getInvoice(){
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key');
    }
    public function getInvoiceCurrencySymbol(){
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key')->select('cur_symbol');
    }
    public function getInvoiceStatus(){
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key')->select('status');
    }
    public function getInvoiceClient(){
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key')->select('clientid');
    }
    public function restoreSplitPayments()
    {
        $this->restore();
    }
    public function paidInvoices()
    {
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key')->where('status', "paid");
    }

    public function dueInvoices()
    {
        return $this->belongsTo(Invoice::class,'invoice_id','invoice_key')->where('status', "due");
    }

}
