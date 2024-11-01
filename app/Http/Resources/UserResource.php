<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected array $relations = [];

    protected array $relation_count = [];

    protected string $source = 'User';

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
    public function additional(array $data): UserResource
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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image' => $this->userImageUrl(),
            'type' => $this->type,
            'trello_id' => $this->trello_id,
            'team_key' => $this->team_key,
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

    private function userImageUrl()
    {
        $image = $this->image;
        if (!$image) {
            return null;
        }
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (file_exists(public_path('assets/images/profile_images/') . $image) && in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif'])) {
            return asset("assets/images/profile_images/{$image}");
        }
        return null;
    }
}
