<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'type',
    //     'image',
    //     'team_key',
    // ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTeamName()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key')->select('name')->withDefault(['name'=>'none']);
    }

    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
    }
    public function websiteViews()
    {
        return $this->hasMany(WebsiteView::class);
    }
    public function getTeamBrands()
    {
        return $this->HasManyThrough(Brand::class,AssignBrand::class, 'team_key', 'brand_key','team_key','brand_key');
    }

    public function setUserBrandEmails()
    {
        return $this->belongsToMany(EmailConfiguration::class, AssignUserBrandEmail::class,  'user_id', 'email_configuration_id');
    }

    public function getUserBrandEmails()
    {
        return $this->HasManyThrough(EmailConfiguration::class,AssignUserBrandEmail::class, 'user_id', 'id','id','email_configuration_id');
    }
    public function getUserBrandEmailNames()
    {
        return $this->HasManyThrough(EmailConfiguration::class,AssignUserBrandEmail::class, 'user_id', 'id','id','email_configuration_id')->select('email');
    }
    public function client()
    {
        return $this->belongsTo(Client::class,'clientid', 'id');
    }

    public function getDepartment()
    {
        return $this->hasManyThrough(Department::class, AssignDepartmentUser::class, 'user_id', 'id', 'id', 'department_id');
    }

    public function setDepartment()
    {
        return $this->belongsToMany(Department::class, AssignDepartmentUser::class, 'user_id', 'department_id');
    }
    public function getLabels()
    {
        return $this->hasMany(Label::class, 'user_id', 'id');
    }
    /** Attribute*/

    public function setAssignedTeamsAttribute($value)
    {
        $this->attributes['assigned_teams'] = json_encode($value);
    }
    public function getAssignedTeamsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getAssignedDepartmentsAttribute()
    {
        return null;
    }
    /** Sync Functionaltity*/
    public function syncAssignedTeams(array $teamKeys)
    {
        $this->assigned_teams = $teamKeys;
        $this->save();
    }
}
