<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTask;
use App\Http\Requests\Task\UpdateTask;
use App\Http\Resources\Task\TaskResource;
use App\Http\Resources\Task\TasksResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = TasksResource::collection(auth()->user()->tasks);
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
            'task' => new TaskResource($task)
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
            'task' => new TaskResource($task)
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
    public function saveOrder(Request $request){
        Task::setNewOrder($request->get('order_array'));

        return response()->json([
            'success' => true,
            'message' => 'New order saved successfully!.'
        ], 200);
    }
}
