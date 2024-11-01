<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardListResource extends JsonResource
{
    protected array $relations = [
        'getBoardListCards' => 'cards',
        'getDepartment' => 'department',
    ];

    protected array $relation_count = [];

    protected string $source = 'Board List';

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
    public function additional(array $data): BoardListResource
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
            'title' => $this->title,
        ];
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

    private function getBoardListCards(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BoardListCardResource::collection($this->whenLoaded('getBoardListCards'));
    }

    private function getDepartment(): ?DepartmentResource
    {
        return new DepartmentResource($this->whenLoaded('getDepartment'));
    }
}
