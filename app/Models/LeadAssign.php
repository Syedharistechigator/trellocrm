<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadAssign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_assigns';
    protected $primaryKey = 'id'; 
    protected $guarded = [];
}
