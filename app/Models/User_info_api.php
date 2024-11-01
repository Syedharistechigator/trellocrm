<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_info_api extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_info_apis';
    protected $primaryKey = 'id';

    protected $fillable = ['key','email','balance'];


    protected $dates = ['deleted_at'];
}
