<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceSignature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_signatures';
    protected $primaryKey = 'id';

    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_key', 'invoice_key');
    }
}
