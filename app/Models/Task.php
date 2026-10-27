<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'status', 'priority', 'category'];

    protected $attributes = ['status' => 'pending', 'priority' => 'medium'];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected function casts(): array
    {
        return [
            'due_date' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
        ];
    }

    public function scopeForListing(Builder $query, array $filters): void
    {
        $search = $filters['search'] ?? null;
        if (is_string($search) && $search !== '') {
            $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($search)).'%';
            $query->whereRaw("LOWER(title) LIKE ? ESCAPE '!'", [$pattern]);
        }

        $direction = $filters['direction'] ?? 'asc';
        $query->orderBy($filters['sort'] ?? 'due_date', $direction)->orderBy('id', $direction);
    }

}
