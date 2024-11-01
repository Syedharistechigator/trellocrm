<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadComments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_comments';
    protected $primaryKey = 'id'; 
    protected $guarded = [];
}
