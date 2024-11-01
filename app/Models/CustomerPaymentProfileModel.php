<?php

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

class CustomerPaymentProfileModel extends Model
{
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'customer_payment_profiles';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['client_id','model_id','model_type','customer_profile_id','response','status',];

    public function fill(array $attributes)
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }

    protected static function getLogEvents()
    {
        /** Events to be logged */
        return [
            'created',
            'updated',
            'deleted',
        ];
    }

    public function shouldBeLogged()
    {
        return true;
    }

    /**
     * @var mixed
     */
    protected static function boot()
    {
        parent::boot();
        self::bootLogActivity();
    }

    /**
     * Get the actor (user or admin) associated with the log action.
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    public function getClient()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /** Scopes Start */


    /** Scopes End*/
}
