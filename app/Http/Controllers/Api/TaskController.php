<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;


class TaskController extends Controller
{
    public function index(IndexTaskRequest $request)
    {
        $filters = $request->validated();

        $tasks = Task::query()
            ->forListing($filters)
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): TaskResource
    {
        return new TaskResource(Task::query()->create($request->validated())->refresh());
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $data = $request->validated();
        if ($request->isMethod('PUT')) {
            $data['description'] ??= null;
        }
        $task->update($data);

        return new TaskResource($task->refresh());
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Удалено']);
    }
}
