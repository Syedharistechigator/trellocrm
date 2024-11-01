<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadStatus::class,'status','id')->select('status')->withDefault(['status'=>'new']);
    }
    public function getStatusColor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadStatus::class,'status','id')->select('leadstatus_color')->withDefault(['leadstatus_color'=>'default']);
    }
    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_key','brand_key')->withTrashed();
    }
    public function getBrandName(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->select('name')->withDefault(['name'=>'none']);
    }
    public function getAgentNames()
    {
        return $this->hasManyThrough(User::class, LeadAssign::class, 'leadid', 'id', 'id', 'userid')->where('users.status', 1)->distinct();
    }


}
