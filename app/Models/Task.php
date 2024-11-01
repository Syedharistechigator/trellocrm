<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
