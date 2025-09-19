@extends('layouts.main')

@section('content')
<div class="container">
    <h2 class="mb-4">Create Task</h2>

    <form action="{{ url('/tasks') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title <span style="color: red;">*</span> </label>
            <input type="text" name="title" class="form-control" placeholder="Enter title" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Enter description"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Task</button>
        <a href="{{ url('/tasks') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
