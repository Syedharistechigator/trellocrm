<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BoardListCardComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'board_list_card_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'board_list_card_id',
        'comment',
        'is_modified',
        'activity_id',
        'created_at',
        'updated_at'
    ];

    protected $dates = ['created_at', 'updated_at' ,'deleted_at'];

    /**
     * Set the created_at attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setCreatedAtAttribute($value)
    {
        if ($value) {
            $this->attributes['created_at'] = Carbon::parse($value, 'Pacific/Honolulu')->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s.u');
        }
    }

    /**
     * Set the updated_at attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAtAttribute($value)
    {
        if ($value) {
            $this->attributes['updated_at'] = Carbon::parse($value, 'Pacific/Honolulu')->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s.u');
        }
    }
    /**
     * Set the deleted_at attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setDeletedAtAttribute($value)
    {
        if ($value) {
            $this->attributes['deleted_at'] = Carbon::parse($value, 'Pacific/Honolulu')->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s.u');
        }
    }

    public function getBoardListCard(){
        return $this->belongsTo(BoardListCard::class,'board_list_card_id','id');
    }
    public function getCommentUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function getActivity(){
        return $this->belongsTo(BoardListCardActivity::class,'activity_id','id');
    }
    public function getPreviousComment(){
        return $this->belongsTo(BoardListCardCommentPreviousLog::class,'comment_id','id');
    }
}