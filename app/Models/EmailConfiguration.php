<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailConfiguration extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'email_configurations';
    protected $primaryKey = 'id';
    protected $fillable = ['created_by','parent_id','brand_key','provider','email','client_id','client_secret','api_key','access_token','status',];

    public function getBrand(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->withTrashed();
    }
    public function getBrandNameWithTrashed(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->withTrashed()->select('name')->withDefault(['name'=>'---']);
    }

    /**
     * Get the parent configuration.
     */
    public function parent()
    {
        return $this->belongsTo(EmailConfiguration::class, 'parent_id');
    }

    /**
     * Get the child configurations.
     */
    public function children()
    {
        return $this->hasMany(EmailConfiguration::class, 'parent_id');
    }
}
