<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->optional()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+3 months', 'UTC'),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'category' => fake()->randomElement(['Work', 'Personal', 'Study', 'Home', 'Health']),
        ];
    }
}
