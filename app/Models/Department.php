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

class Department extends Model
{
    /**
     * Developer michael update
     */
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['name', 'status', 'created_at', 'updated_at', 'deleted_at'];

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

    public function getBoardLists(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(BoardList::class, AssignDepartmentBoardList::class, 'department_id', 'id', 'id', 'board_list_id')->orderBy('position');
    }

    public function getUsers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(User::class, AssignDepartmentUser::class, 'department_id', 'id', 'id', 'user_id');
    }
}
