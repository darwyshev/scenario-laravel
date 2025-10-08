<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login Admin</h2>
    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif
    <form method="POST" action="{{ route('prosesLogin') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>

</html>