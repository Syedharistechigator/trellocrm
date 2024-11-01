<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    public function label()
    {
        return $this->hasMany(Label::class, 'color_id', 'id');
    }

    public function assign_board_labels()
    {
        return $this->hasMany(AssignBoardLabel::class, 'label_id');
    }
}
