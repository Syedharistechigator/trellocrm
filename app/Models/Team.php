<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;


class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teams';
    protected $primaryKey = 'id';

    protected $fillable = ['team_key', 'name', 'status', 'team_lead'];

    protected $dates = ['deleted_at'];


    /**
     * Set the categories
     *
     */
    public function setBrandAttribute($value)
    {
        $this->attributes['brand'] = json_encode($value);
    }

    /**
     * Get the categories
     *
     */
    public function getBrandAttribute($value)
    {
        return $this->attributes['brand'] = json_decode($value);
    }

    public function getBrands()
    {
        return $this->hasManyThrough(Brand::class, AssignBrand::class, 'team_key', 'brand_key', 'team_key', 'brand_key');
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, 'team_key', 'team_key');
    }
    public function getTmPpcUsers()
    {
        return $this->hasMany(User::class, 'team_key', 'team_key')->where('type','tm-ppc');
    }
}

