<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class AssignBoardCard extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    public $timestamps = false;
    protected $table = 'assign_board_cards';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

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

}
