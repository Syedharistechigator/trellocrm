<?php /** Dm => Michael Update */

namespace App\Models;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PaymentAuthorization extends Model
{
    /**
     * Developer michael update
     */
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'payment_authorizations';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['invoice_id', 'client_id', 'card_id', 'payment_gateway', 'merchant_id', 'transaction_id', 'response', 'response_status','payment_status'];

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
