<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function getTasks(User $user, array $filters): LengthAwarePaginator
    {
        $query = $user->tasks();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'ilike', '%'.$filters['search'].'%');
        }

        if (isset($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

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

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function createTask(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    public function getSummary(User $user): array
    {
        $query = $user->tasks();

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'high_priority' => (clone $query)->where('priority', 'high')->count(),
            'medium_priority' => (clone $query)->where('priority', 'medium')->count(),
            'low_priority' => (clone $query)->where('priority', 'low')->count(),
        ];
    }
}
