<?php

namespace App\Http\Resources;

use App\Traits\BoardListDateFormatTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardListCardAttachmentResource extends JsonResource
{
    use BoardListDateFormatTrait;

    protected array $relations = [];

    protected array $relation_count = [];

    protected string $source = 'Attachment';

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
    public function additional(array $data): BoardListCardAttachmentResource
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
            'user_id' => $this->user_id,
            'activity_id' => $this->activity_id,
            'original_name' => $this->original_name,
            'file_name' => $this->file_name,
        ];
//        if ($this->source !== 'Activity') {
        $data['mime_type'] = $this->mime_type;
        $data['file_size'] = $this->file_size;
//        }
        foreach ($this->relations as $relationMethod => $keyName) {
            if ($this->relationLoaded($relationMethod)) {
                $data[$keyName] = $this->{$relationMethod}();
                if (in_array($relationMethod, $this->relation_count, true)) {
                    $data[$keyName . '_count'] = $this->{$relationMethod}->count();
                }
            }
        }
        if (!$isDeleted) {
            $data['file_path'] = $this->imageUrl();
            $data['created_at'] = $this->formatTimestamp($this->created_at);
        }
        if ($isDeleted && $this->source !== 'Attachment') {
            $data['deleted_at'] = $this->formatTimestamp($this->deleted_at);
        }
        if (!empty($this->additionalFields)) {
            $data = array_merge($data, $this->additionalFields);
        }
        return $data;
    }

    private function imageUrl(): string
    {
        $file_directory = str_contains($this->mime_type, 'image') ? 'images' : 'images';
//        $clientpath = optional($this->getActivity->getBoardListCard ?? $this->getBoardListCard)->client_id ?? "random-client";

        $path = "assets/{$file_directory}/board-list-card/original";
        $directories = [
            "{$path}/{$this->file_name}",
//            "{$path}/{$clientpath}/{$this->file_name}",
//            "{$path}/random-client/{$this->file_name}",
//            "{$path}/random-client/{$this->user_id}/{$this->board_list_card_id}/{$this->file_name}",
        ];
        foreach ($directories as $directory) {
            $fullPath = public_path($directory);
            if (file_exists($fullPath)) {
                return asset($directory);
            }
        }
        return asset("assets/images/no-results-found.png");
    }
}
