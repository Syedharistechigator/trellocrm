<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BoardListCardCommentPreviousLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'board_list_card_comment_previous_logs';
    protected $primaryKey = 'id';

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

    public function getOldComment(){
        return $this->belongsTo(BoardListCardComment::class,'comment_id','id');
    }
}
