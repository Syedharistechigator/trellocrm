<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AssignUserBrandEmail extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'assign_user_brand_emails';
    protected $primaryKey = 'id';
}
