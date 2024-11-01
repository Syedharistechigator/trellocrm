<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $guarded = [];

    /** Scopes Start*/
    public function scopeYearlyExpense($query)
    {
        return $query->where('status', 1)->whereYear('created_at', now()->format('Y'));
    }
    /** Scopes End*/

}
