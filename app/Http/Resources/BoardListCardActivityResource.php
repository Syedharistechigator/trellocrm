<?php

namespace App\Http\Resources;

use App\Traits\BoardListDateFormatTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardListCardActivityResource extends JsonResource
{
    use BoardListDateFormatTrait;

    protected array $relations = [
        'getAttachmentWithTrashed' => 'attachment',
        'getCommentWithTrashed' => 'comment',
        'getUser' => 'activity_user',
    ];
    protected array $relation_count = [];

    protected string $source = 'Activity';

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
    public function additional(array $data): BoardListCardActivityResource
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
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'activity' => $this->activity,
            'activity_type' => $this->activity_type,
            'activity_user' => $this->getUser(),
            'created_at' => $this->formatTimestamp($this->created_at),
        ];
        foreach ($this->relations as $relationMethod => $keyName) {
            if ($this->relationLoaded($relationMethod)) {
                if ($this->activity_type == 0 && $relationMethod == 'getCommentWithTrashed') {
                    $data[$keyName] = $this->{$relationMethod}();
                    $data['comment_edited'] = isset($this->getCommentWithTrashed->getPreviousComment->id);
                } elseif ($this->activity_type == 1 && $relationMethod == 'getAttachmentWithTrashed') {
                    $data[$keyName] = $this->{$relationMethod}();
                }
                if (in_array($relationMethod, $this->relation_count, true) && !in_array($relationMethod, ['getAttachmentWithTrashed', 'getCommentWithTrashed'])) {
                    $data[$keyName] = $this->{$relationMethod}();
                    $data[$keyName . '_count'] = $this->{$relationMethod}->count();
                }
            }
        }
        $isDeleted = false;
        if ($this->activity_type == 1) {
            $isDeleted = optional($this->getAttachmentWithTrashed)->trashed();
        } elseif ($this->activity_type == 0) {
            $isDeleted = optional($this->getCommentWithTrashed)->trashed();
        }
        if ($this->activity_type != 2) {
            $data['activity_2'] = $isDeleted ? 'from this card' : 'to this card';
        }
        if (!empty($this->additionalFields)) {
            $data = array_merge($data, $this->additionalFields);
        }
        return $data;
    }

    private function getUser(): UserResource
    {
        return new UserResource($this->whenLoaded('getUser'), 'Activity');
    }

    private function getAttachmentWithTrashed(): ?BoardListCardAttachmentResource
    {
        if ($this->activity_type == 1) {
            return new BoardListCardAttachmentResource($this->getRelation('getAttachmentWithTrashed'), 'Activity');
        }
        return null;
    }

    private function getCommentWithTrashed(): ?BoardListCardCommentResource
    {
        if ($this->activity_type == 0) {
            return new BoardListCardCommentResource($this->getRelation('getCommentWithTrashed'), 'Activity');
        }
        return null;
    }

}
