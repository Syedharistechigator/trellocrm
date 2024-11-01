<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class WebhookResponse extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'webhook_responses';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = ['merchant_name','merchant_id','merchant_type','notification_id','webhook_id','event_type','event_date','response','status'];

    public function fill(array $attributes)
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }
}
