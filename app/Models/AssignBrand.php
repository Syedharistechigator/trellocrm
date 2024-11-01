<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignBrand extends Model
{
    use HasFactory;

    protected $table = 'assign_brands';

    protected $fillable = ['team_key','brand_key'];

    public function getBrand(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key');
    }
    public function getBrandWithOutTrashed(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->withoutTrashed();
    }
    public function getBrandNameWithTrashed(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->withTrashed()->select('name')->withDefault(['name'=>'---']);
    }
    public function getBrandName(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->select('name')->withDefault(['name'=>'---']);
    }
    public function getBrandUrl(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->select('brand_url')->withDefault(['brand_url'=>'---']);
    }
}
