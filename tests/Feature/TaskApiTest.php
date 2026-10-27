<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'title' => 'Complete project',
            'description' => 'Finish implementation',
            'due_date' => '2026-11-01T15:00:00Z',
            'category' => 'Work',
            'status' => 'pending',
            'priority' => 'medium',
        ];
    }

    public function test_create_task(): void
    {
        $response = $this->postJson('/api/tasks', $this->payload())
            ->assertCreated()->assertJsonPath('data.title', 'Complete project');

        $this->assertDatabaseHas('tasks', ['id' => $response->json('data.id'), 'title' => 'Complete project']);
    }

    public function test_list_tasks(): void
    {
        $task = Task::factory()->create();

        $this->getJson('/api/tasks')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $task->id);
    }

    public function test_show_task(): void
    {
        $task = Task::factory()->create();

        $this->getJson('/api/tasks/'.$task->id)->assertOk()
            ->assertJsonPath('data.id', $task->id)->assertJsonPath('data.title', $task->title);
    }

    public function test_replace_task(): void
    {
        $task = Task::factory()->create();

        $this->putJson('/api/tasks/'.$task->id, $this->payload())->assertOk()
            ->assertJsonPath('data.title', 'Complete project');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Complete project', 'category' => 'Work']);
    }

    public function test_patch_task(): void
    {
        $task = Task::factory()->create(['title' => 'Original']);

        $this->patchJson('/api/tasks/'.$task->id, ['status' => 'completed'])->assertOk()
            ->assertJsonPath('data.status', 'completed')->assertJsonPath('data.title', 'Original');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed', 'title' => 'Original']);
    }

    public function test_delete_task(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson('/api/tasks/'.$task->id)->assertOk()->assertExactJson(['message' => 'Удалено']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

}
