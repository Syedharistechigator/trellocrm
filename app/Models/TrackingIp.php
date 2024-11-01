<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class TrackingIp extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tracking_ips';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
