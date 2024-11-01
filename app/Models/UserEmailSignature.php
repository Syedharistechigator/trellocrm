<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEmailSignature extends Model
{

    use HasFactory,SoftDeletes;
    protected $table = 'user_email_signatures';
    protected $primaryKey = 'id';
    protected $fillable = ['email_configuration_id','user_id','signature','status'];
}
