<!DOCTYPE html>
<html>
<head>
    <title>Task App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="{{ route('tasks.index') }}">TaskApp</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger btn-sm">Logout</button>
          </form>
        </li>
        @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register.form') }}">Register</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    @yield('content')
    @yield('scripts')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: '{{ implode(', ', $errors->all()) }}',
        });
    @endif
</script>
</body>
</html>
