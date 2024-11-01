<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $invoice_id
 * @property int $payment_gateway
 */
class CcInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cc_infos';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['invoice_id', 'payment_gateway'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function getInvoices()
    {
        return $this->belongsTo(Invoice::class, 'client_id', 'clientid');
    }

}
