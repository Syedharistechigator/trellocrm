<?php /** Dm => Michael Update */

namespace App\Models;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AssignDepartmentUser extends Model
{
    /**
     * Developer michael update
     */
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'assign_department_users';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['department_id', 'user_id'];

    public function fill(array $attributes)
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }

    protected static function getLogEvents()
    {
        /** Events to be logged */
        return [
            'created',
            'updated',
            'deleted',
        ];
    }

    public function shouldBeLogged()
    {
        return true;
    }

    /**
     * @var mixed
     */
    protected static function boot()
    {
        parent::boot();
        self::bootLogActivity();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

}
