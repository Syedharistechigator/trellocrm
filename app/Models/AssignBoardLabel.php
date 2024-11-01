<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class AssignBoardLabel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'board_list_card_id',
        'user_id',
        'label_id',
    ];

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

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'label_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
