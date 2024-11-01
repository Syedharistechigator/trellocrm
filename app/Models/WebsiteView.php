<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteView extends Model
{
    use HasFactory;

    protected $table = 'website_views';
    protected $primaryKey = 'id';
    protected $fillable = ['page_url','ip_address','ip_response',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
