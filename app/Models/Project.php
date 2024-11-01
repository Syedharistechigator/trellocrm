<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


class Project extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getClient()
    {
        return $this->belongsTo(Client::class, 'clientid', 'id');
    }
    public function getClientUser()
    {
        return $this->belongsTo(User::class, 'clientid', 'clientid');
    }
    public function getAgent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }
    public function getStatus()
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status', 'id');
    }
    public function getBrandName(){
        return $this->belongsTo(Brand::class,'brand_key','brand_key')->select('name')->withDefault(['name'=>'---']);
    }
    public function remainingDays()
    {
        if ($this->project_date_due) {
            $dueDate = Carbon::parse($this->project_date_due);
            $currentDate = Carbon::now();
            $daysMissed = $dueDate->diffInDays($currentDate);
            if ($daysMissed === 0) {
                $message = abs($daysMissed) . ' Day(s) left.';
                $badgeClass = 'badge-danger';
            }elseif ($daysMissed < 0) {
                $message = abs($daysMissed) . ' Day(s) deadline missed.';
                $badgeClass = 'badge-danger';
            } else {
                $message = max(1, $daysMissed) . ' Day(s) available till due date.';
                $badgeClass = 'badge-success';
            }
            return [
                'message' => $message,
                'badgeClass' => $badgeClass,
            ];
        }
        return [
            'message' => 'Due date not set.',
            'badgeClass' => 'badge-secondary', // Or any other appropriate color for no due date
        ];
    }
}
