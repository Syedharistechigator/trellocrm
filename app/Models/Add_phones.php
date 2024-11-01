<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Add_phones extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'add_phones';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
