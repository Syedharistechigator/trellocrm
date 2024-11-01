<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class MultiPaymentResponse extends Model
{

    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'multi_payment_responses';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['invoice_id', 'response', 'payment_gateway', 'payment_process_from', 'form_inputs','response_status', 'controlling_code'];

    public function fill(array $attributes)
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }
    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_key')->withTrashed();
    }
}
