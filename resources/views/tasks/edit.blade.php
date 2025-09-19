@extends('layouts.main')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Task</h2>

    <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title <span style="color: red;">*</span> </label>
            <input type="text" name="title" class="form-control"
                   value="{{ $task->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ $task->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="is_completed" class="form-select">
                <option value="0" {{ $task->is_completed == '0' ? 'selected' : '' }}>Not Completed</option>
                <option value="1" {{ $task->is_completed == '1' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Task</button>
        <a href="{{ url('/tasks') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
