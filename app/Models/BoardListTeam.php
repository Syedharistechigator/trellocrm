<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class BoardListTeam extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'board_list_teams';
    protected $primaryKey = 'id';
    protected $fillable = ['board_list_id', 'team_key'];
}
