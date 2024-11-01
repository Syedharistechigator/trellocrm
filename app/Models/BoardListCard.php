<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class BoardListCard extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'board_list_cards';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description', 'cover_image', 'cover_image_updated_at','trello_url'];

    protected $dates = ['cover_image_updated_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Set the created_at attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setCreatedAtAttribute($value)
    {
        if ($value) {
            $this->attributes['created_at'] = Carbon::parse($value, 'Pacific/Honolulu')->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s.u');
        }
    }

    public function setCoverImageAttribute($value)
    {
        if ($this->attributes['cover_image'] !== $value) {
            $this->attributes['cover_image_updated_at'] = Carbon::now('Asia/Karachi');
        }
        $this->attributes['cover_image'] = $value;
    }

    /**
     * Set the updated_at attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAtAttribute($value)
    {
        if ($value) {
            $this->attributes['updated_at'] = Carbon::parse($value, 'Pacific/Honolulu')->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s.u');
        }
    }

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
//            $maxPosition = self::where('board_list_id', $card->board_list_id)->max('position');
            $card->code = $card->encrypt();
//            $card->position = $maxPosition + 1;
            if (!isset($card->position) || $card->position === null) {
                $card->position = 0;
            }

            $card->reorderPositions($card->board_list_id);

        });

        static::created(function ($card) {
            $card->code = $card->encrypt();
            $card->save();
        });
    }

    /**
     * Reorder the positions of cards in the given board list to remove gaps.
     *
     * @param int $board_list_id
     */
    public function reorderPositions(int $board_list_id): void
    {
        $board_list_cards = BoardListCard::where('board_list_id', $board_list_id)
            ->orderBy('position', 'asc')
            ->get();
        $position = 1;
        foreach ($board_list_cards as $card) {
            $card->position = $position++;
            $card->save();
        }
    }

    public function encrypt(): string
    {
        return rtrim(base64_encode("DM" . $this->id . "CARD"), '=');
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
            } elseif ($daysMissed < 0) {
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

    public function getBoardList()
    {
        return $this->belongsTo(BoardList::class, 'board_list_id', 'id');
    }

    public function getClient()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function getBoardListCardUsers()
    {
        return $this->HasManyThrough(User::class, AssignBoardCard::class, 'board_list_card_id', 'id', 'id', 'user_id');
    }

    public function setUsers()
    {
        return $this->belongsToMany(User::class, AssignBoardCard::class, 'board_list_card_id', 'user_id');
    }

    public function getBoardListLabels()
    {
        return $this->hasMany(AssignBoardLabel::class);
    }

    public function getLabels()
    {
        return $this->hasMany(Label::class, 'board_list_card_id', 'id');
    }

//    public function setLabels(){
//        return $this->hasMany(Label::class,'board_list_card_id','id');
//    }

    public function setLabels()
    {
        return $this->hasMany(Label::class, 'board_list_card_id');
    }

    public function assignLabels()
    {
        return $this->belongsToMany(Label::class, AssignBoardLabel::class, 'board_list_card_id', 'label_id');
    }

    public function setLabelUsers()
    {
        return $this->belongsToMany(User::class, AssignBoardLabel::class, 'board_list_card_id', 'user_id');
    }

    public function getComments()
    {
        return $this->hasMany(BoardListCardComment::class, 'board_list_card_id', 'id');
    }

    public function getCommentWithTrashed()
    {
        return $this->hasMany(BoardListCardComment::class, 'board_list_card_id', 'id')->withTrashed();
    }

    public function getAttachments()
    {
        return $this->hasMany(BoardListCardAttachment::class, 'board_list_card_id', 'id');
    }

    public function getAttachmentsWithTrashed()
    {
        return $this->hasMany(BoardListCardAttachment::class, 'board_list_card_id', 'id')->withTrashed();
    }

    public function getActivities()
    {
        return $this->hasMany(BoardListCardActivity::class, 'board_list_card_id', 'id');
    }

    public function getActivitiesByDescCreated()
    {
        return $this->hasMany(BoardListCardActivity::class, 'board_list_card_id', 'id')->orderBy('created_at', 'desc');
    }

    public function getCommentUser()
    {
        return $this->HasManyThrough(User::class, BoardListCardComment::class, 'board_list_card_id', 'id', 'id', 'user_id');
    }

    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'team_key', 'team_key');
    }
}
