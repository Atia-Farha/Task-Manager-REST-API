<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(IndexTaskRequest $request)
    {
        $this->authorize('viewAny', Task::class);

        $validated = $request->validated();

        $query = $request->user()->tasks();

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['priority'])) {
            $query->where('priority', $validated['priority']);
        }

        if (isset($validated['search'])) {
            $query->where('title', 'ilike', '%'.$validated['search'].'%');
        }

        if (isset($validated['created_from'])) {
            $query->whereDate('created_at', '>=', $validated['created_from']);
        }

        if (isset($validated['created_to'])) {
            $query->whereDate('created_at', '<=', $validated['created_to']);
        }

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortOrder = $validated['sort_order'] ?? 'desc';

        if ($sortBy === 'priority') {
            $query->orderByRaw(
                "CASE priority
					WHEN 'low' THEN 1
					WHEN 'medium' THEN 2
					WHEN 'high' THEN 3
				END {$sortOrder}"
            );
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $query->orderBy('id', 'desc');

        return TaskResource::collection($query->paginate($validated['per_page'] ?? 10))
            ->additional([
                'success' => true,
            ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

        $task = $request->user()->tasks()->create($validated);

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

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($task->fresh()),
        ]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function summary(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $query = $request->user()->tasks();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => (clone $query)->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'high_priority' => (clone $query)->where('priority', 'high')->count(),
                'medium_priority' => (clone $query)->where('priority', 'medium')->count(),
                'low_priority' => (clone $query)->where('priority', 'low')->count(),
            ],
        ]);
    }
}
