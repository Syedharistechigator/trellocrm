<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cards';
    protected $primaryKey = 'id';
    protected $fillable = ['title','team_id','position','sort_tasks'];
    protected $dates = ['deleted_at'];
}
