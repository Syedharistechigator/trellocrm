<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class BoardList extends Model
{

    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'board_lists';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'team_key', 'position', 'sort_tasks', 'status'];
    protected $dates = ['deleted_at'];

//    protected static function boot()
//    {
//        parent::boot();
//        static::deleting(function ($boardList) {
//            $boardList->getBoardListTeams()->delete();
//        });
//    }
    public function getBoardListCards()
    {
        return $this->hasMany(BoardListCard::class, 'board_list_id');
    }

    public function setteams()
    {
        return $this->belongsToMany(Team::class, BoardListTeam::class, 'board_list_id', 'team_key');
    }

    public function getBoardListTeams()
    {
        return $this->hasMany(BoardListTeam::class, 'board_list_id', 'id');
    }

    public function getTeams()
    {
        return $this->HasManyThrough(Team::class, BoardListTeam::class, 'board_list_id', 'team_key', 'id', 'team_key');
    }

    public function getDepartment()
    {
        return $this->hasOneThrough(Department::class, AssignDepartmentBoardList::class, 'board_list_id', 'id', 'id', 'department_id');
    }

    public function setDepartment()
    {
        return $this->belongsToMany(Department::class, AssignDepartmentBoardList::class, 'board_list_id', 'department_id');
    }
}
/**
 * Through Table (second or bridge table) First Key == first table foreign key
 * Through Table (second or bridge table) Second Local Key ==  third table foreign key
 * Related Table (third table) Second Key == primary key if id is not primary must change
 * Main Model in which we are defining (first table) Second Key == primary key if id is not primary must change
 */
