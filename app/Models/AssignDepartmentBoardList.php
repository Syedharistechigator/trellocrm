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

class AssignDepartmentBoardList extends Model
{
    /**
     * Developer michael update
     */
    use HasFactory, Notifiable, SoftDeletes, LogActivityTrait;

    protected $table = 'assign_department_board_lists';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['department_id', 'board_list_id'];

    public function fill(array $attributes): AssignDepartmentBoardList
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        $attributes = array_intersect_key($attributes, array_flip($columns));
        return parent::fill($attributes);
    }

    protected static function getLogEvents(): array
    {
        /** Events to be logged */
        return [
            'created',
            'updated',
            'deleted',
        ];
    }

    public function shouldBeLogged(): bool
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

    public function getDepartment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'id');
    }

    public function getBoardList(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BoardList::class, 'id');
    }

}
