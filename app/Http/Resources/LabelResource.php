<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabelResource extends JsonResource
{
    protected array $relations = [
        'color' => 'color',
    ];

    protected array $relation_count = [];

    protected string $source = 'Label';

    protected array $additionalFields = [];

    protected ?int $card_id = null;

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
    public function additional(array $data): LabelResource
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
            'user' => isset($this->getLabelUser) ? $this->getLabelUser() : null,
            'board_list_card_id' => $this->board_list_card_id,
            'label' => $this->label_text !== null ? ucfirst($this->label_text) : $this->label_text,
            'assigned' => $this->isAssigned(),
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

    private function color(): BoardListCardColorResource
    {
        return new BoardListCardColorResource($this->whenLoaded('color'));
    }

    private function getLabelUser(): UserResource
    {
        return new UserResource($this->whenLoaded('getLabelUser'));
    }

    private function isAssigned()
    {
        if (isset($this->assign_board_labels)) {
            if ($this->getLabelUser) {
                return $this->assign_board_labels->board_list_card_id === $this->board_list_card_id;
            }
            return true;
        }
        return false;
    }

}
