<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->filter ?? 'all';
        $query = Task::where('user_id', auth()->id());

        if ($filter === 'completed') {
            $query->where('is_completed', 1);
        } elseif ($filter === 'incomplete') {
            $query->where('is_completed', 0);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('order')->get();

        return view('tasks.index', compact('tasks', 'filter'));
    }

    public function create()
    {
        return view('tasks.created');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created!');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $task->update($request->only('title', 'description', 'order', 'is_completed'));

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $taskId) {
            Task::where('id', $taskId)
                ->where('user_id', auth()->id())
                ->update(['order' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }

    public function complete(Request $request, Task $task)
    {
        $task->update(['is_completed' => 1]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task completed!');
    }
}
