<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date->utc()->toISOString(),
            'created_at' => $this->created_at?->utc()->toISOString(),
            'updated_at' => $this->updated_at?->utc()->toISOString(),
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'category' => $this->category,
        ];
    }
}
