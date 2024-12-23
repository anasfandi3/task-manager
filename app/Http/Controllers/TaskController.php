<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTask;
use App\Http\Requests\Task\UpdateTask;
use App\Http\Resources\Task\TasksResource;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = TasksResource::collection(Task::all());
        return response()->json(compact('tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTask $request)
    {
        $validated = $request->validated();
        $task = Task::create([
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'completed' => $validated['completed'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTask $request, Task $task)
    {
        $validated = $request->validated();
        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.'
        ], 200);
    }
}
