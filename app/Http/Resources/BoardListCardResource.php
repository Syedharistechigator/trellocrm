<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Traits\BoardListCardCoverImageTrait;
use App\Traits\BoardListDateFormatTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardListCardResource extends JsonResource
{
    use BoardListCardCoverImageTrait, BoardListDateFormatTrait;

    protected array $relations = [
        'getBoardList' => 'board_list',
        'getClient' => 'client',
        'getLabels' => 'labels',
        'getLabels.color' => 'color',
        'getActivities' => 'activities',
        'getAttachments' => 'attachments',
        'getComments' => 'comments',
        'getBoardListCardUsers' => 'assigned_users',
        'getTeam' => 'team',
    ];

    protected array $relation_count = [
        'getLabels',
        'getActivities',
        'getAttachments',
        'getComments',
        'getBoardListCardUsers',
    ];

    protected string $source = 'Board List Card';

    protected array $additionalFields = [];

    public function __construct($resource, $source = null)
    {
        parent::__construct($resource);
        $this->source = $source ?? $this->source;
    }

    /**
     * Set additional fields to be included in the response.
     *
     * @param array $data
     * @return $this
     */
    public function additional(array $data): BoardListCardResource
    {
        $this->additionalFields = $data;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $diffInMinutes = null;
        if ($this->cover_image_updated_at) {
            $activityTimezone = 'Asia/Karachi';
            $timestamp = Carbon::parse($this->cover_image_updated_at)->timezone($activityTimezone)->subHours(15);
            $diffInMinutes = Carbon::now($activityTimezone)->diffInMinutes($timestamp);
        }
        $data = [
            'id' => $this->id,
            'team_key' => $this->team_key,
            'department_id' => null,
            'team' => null,
            'board_list' => null,
            'client' => null,
            'title' => $this->title,
            'description' => $this->description,
            'cover_image' => $this->cover_image ? "original/{$this->cover_image}" : null,
//            'cover_image' => $this->cover_image_url_trait($this),
            'cover_image_updated_at' => $this->cover_image_updated_at ? $this->formatTimestamp($this->cover_image_updated_at) : null,
            'cover_time_difference' => $diffInMinutes,
        ];

        $data["cover_image_thumbnail"] = $this->cover_image ? "150x150/{$this->cover_image}" : null;
//        $data["cover_image_thumbnail"] = $this->cover_image_url_trait($this, 'thumbnail');

        $data['cover_background_color'] = $this->cover_background_color;
        $data['priority'] = $this->priority;
        $data['is_check_start_date'] = $this->is_check_start_date;
        $data['start_date'] = $this->start_date;
        $data['is_check_due_date'] = $this->is_check_due_date;
        $data['due_date'] = $this->due_date;
        $data['task_completed'] = $this->task_completed;
        foreach ($this->relations as $relationMethod => $keyName) {
            if ($this->relationLoaded($relationMethod)) {
                $data[$keyName] = $this->{$relationMethod}();
                if (in_array($relationMethod, $this->relation_count)) {
                    $data[$keyName . '_count'] = $this->{$relationMethod}->count();
                }
            }
        }
        $data['position'] = $this->position;
        $data['department_id'] = optional($this->getBoardList->getDepartment)->id;

        if ($this->relationLoaded('getBoardListCardUsers')) {
            $data['unassigned_users'] = UserResource::collection($this->getBoardListCardUnAssignedUsers());
            $data['unassigned_users_count'] = optional($this->getBoardListCardUnAssignedUsers())->count();
        }
        /** Load Relation After Loop */
        if (!$this->relationLoaded('getAttachments')) {
            $data['attachments_count'] = $this->getAttachments->count();
        }
        if (!$this->relationLoaded('getComments')) {
            $data['comments_count'] = $this->getComments->count();
        }
        $data['code'] = $this->encrypt();
        $data['trello_url'] = $this->trello_url;
        if (!empty($this->additionalFields)) {
            $data = array_merge($data, $this->additionalFields);
        }
        if ($this->deleted_at !== null) {
            $data['deleted_at'] = $this->deleted_at;
        }
        return $data;
    }

    public function encrypt(): string
    {
        return rtrim(base64_encode("DM" . $this->id . "CARD"), '=');
    }

    private function getBoardList(): BoardListResource
    {
        return new BoardListResource($this->whenLoaded('getBoardList'));
    }

    private function getTeam(): TeamResource
    {
        return new TeamResource($this->whenLoaded('getTeam'));
    }

    private function getClient(): ClientResource
    {
        return new ClientResource($this->whenLoaded('getClient'));
    }

    private function getLabels(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return LabelResource::collection($this->whenLoaded('getLabels'));
    }

    private function getActivities(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BoardListCardActivityResource::collection($this->whenLoaded('getActivities')->sortByDesc('created_at'));
        // return BoardListCardActivityResource::collection($this->whenLoaded('getActivities')->sortByDesc('id'));
    }

    private function getAttachments(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BoardListCardAttachmentResource::collection(
            $this->whenLoaded('getAttachments')->sortByDesc('created_at')
        );
    }

    private function getComments(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BoardListCardCommentResource::collection($this->whenLoaded('getComments'));
    }

    private function getBoardListCardUsers(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection($this->whenLoaded('getBoardListCardUsers'));
    }


    public function getBoardListCardUnAssignedUsers()
    {
        return User::where('type', '!=', 'client')
            ->where('status', 1)
            ->whereNotIn('id', $this->getBoardListCardUsers->pluck('id')->all())
            ->get(['id', 'name', 'email','image', 'type', 'trello_id','team_key']);
    }
}
