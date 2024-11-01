<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminView extends Model
{
    use HasFactory;

    protected $table = 'admin_views';
    protected $primaryKey = 'id';
    protected $fillable = ['admin_id','page_url','ip_address','ip_response',];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
