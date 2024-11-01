<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    protected array $relations = [];

    protected array $relation_count = [];

    protected string $source = 'Client';

    protected array $additionalFields = [];

    /**
     * Set additional fields to be included in the response.
     *
     * @param array $data
     * @return $this
     */
    public function additional(array $data): ClientResource
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
            'name' => $this->name,
            'email' => $this->email,
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
}
