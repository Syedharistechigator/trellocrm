<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_list_card_id',
        'user_id',
        'color_id',
        'label_text',
    ];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function assign_board_labels()
    {
        return $this->hasOne(AssignBoardLabel::class, 'label_id', 'id');
    }

    public function getLabelUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function scopeAssigned($query)
    {
        return $query->whereHas('assign_board_labels');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereDoesntHave('assign_board_labels');
    }
}
