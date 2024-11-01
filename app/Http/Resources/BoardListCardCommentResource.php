<?php

namespace App\Http\Resources;

use App\Traits\BoardListDateFormatTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardListCardCommentResource extends JsonResource
{
    use BoardListDateFormatTrait;
    protected array $relations = [];

    protected array $relation_count = [];

    protected string $source = 'Comment';

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
    public function additional(array $data): BoardListCardCommentResource
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
        $isDeleted = $this->trashed();
        $data = [
            'id' => $this->id,
            'board_list_card_id' => $this->board_list_card_id,
            'activity_id' => $this->activity_id,
            'comment' => $this->comment,
        ];
        if (!$isDeleted) {
            $data['created_at'] = $this->formatTimestamp($this->created_at);
        }
        if ($isDeleted && $this->source !== 'Comment') {
            $data['deleted_at'] = $this->formatTimestamp($this->deleted_at);
        }
        foreach ($this->relations as $relationMethod => $keyName) {
            if ($this->relationLoaded($relationMethod)) {
                $data[$keyName] = $this->{$relationMethod}();
                if (in_array($relationMethod, $this->relation_count, true)) {
                    $data[$keyName . '_count'] = $this->{$relationMethod}->count();
                }
            }
        }
        if (!empty($this->additionalFields)) {
            $data = array_merge($data, $this->additionalFields);
        }
        return $data;
    }
}
