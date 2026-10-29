<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexTaskRequest;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(IndexTaskRequest $request, TaskService $taskService)
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $taskService->getTasks(
            $request->user(),
            $request->validated()
        );

        return TaskResource::collection($tasks)
            ->additional([
                'success' => true,
            ]);
    }

    public function store(StoreTaskRequest $request, TaskService $taskService)
    {
        $this->authorize('create', Task::class);

        $task = $taskService->createTask(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => new TaskResource($task),
        ], 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json([
            'success' => true,
            'data' => new TaskResource($task),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task, TaskService $taskService)
    {
        $this->authorize('update', $task);

        $updatedTask = $taskService->updateTask(
            $task,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($updatedTask),
        ]);
    }

    public function destroy(Task $task, TaskService $taskService)
    {
        $this->authorize('delete', $task);

        $taskService->deleteTask($task);

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function summary(Request $request, TaskService $taskService)
    {
        $this->authorize('viewAny', Task::class);

        $summary = $taskService->getSummary($request->user());

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
