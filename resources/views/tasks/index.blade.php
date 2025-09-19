@extends('layouts.main')

@section('content')
    <h2 class="mb-4">Task List</h2>

    <div class="row mb-3">
        <div class="col-md-7">
            <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks..."
                value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <form method="GET" action="{{ route('tasks.index') }}">
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Tasks</option>
                    <option value="completed" {{ $filter == 'completed' ? 'selected' : '' }}>Completed Tasks</option>
                    <option value="incomplete" {{ $filter == 'incomplete' ? 'selected' : '' }}>Incomplete Tasks</option>
                </select>
            </form>
        </div>
        <div class="col-md-2">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3 w-100">Add Task</a>
        </div>
    </div>



    <table class="table table-bordered" id="tasks-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr data-id="{{ $task->id }}">
                    <td style="cursor: grab;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu">
                            <path d="M4 5h16" />
                            <path d="M4 12h16" />
                            <path d="M4 19h16" />
                        </svg>
                    </td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->is_completed ? 'Completed' : 'Incomplete' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" id="taskActions{{ $task->id }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                &#x22EE;
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="taskActions{{ $task->id }}">
                                @if ($task->is_completed == 0)
                                    <li>
                                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST"
                                            class="m-0 p-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Complete</button>
                                        </form>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('tasks.edit', $task->id) }}">Edit</a>
                                </li>
                                <li>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                        class="m-0 p-0 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item delete-btn">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

    <script>
        $(document).ready(function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            function debounce(func, delay) {
                let timer;
                return function() {
                    let context = this,
                        args = arguments;
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(context, args), delay);
                };
            }

            $('#taskSearch').on('input', debounce(function() {
                let search = $(this).val();
                let filter = $('select[name="filter"]').val() || 'all';
                window.location.href = "{{ route('tasks.index') }}" + "?search=" + encodeURIComponent(
                    search) + "&filter=" + filter;
            }, 500));

            $(".delete-btn").click(function() {
                let form = $(this).closest("form");
                Swal.fire({
                    title: "Are you sure?",
                    text: "You cannot undone this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Delete"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: "{!! implode('<br>', $errors->all()) !!}"
                });
            @endif

            $("#tasks-table tbody").sortable({
                placeholder: "ui-state-highlight",
                update: function(event, ui) {
                    let order = [];
                    $("#tasks-table tbody tr").each(function() {
                        order.push($(this).data('id'));
                    });

                    $.ajax({
                        url: "{{ route('tasks.reorder') }}",
                        method: "POST",
                        data: {
                            order: order,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Updated!'
                                });
                            }
                        }
                    });
                }
            }).disableSelection();
        });
    </script>
@endsection
