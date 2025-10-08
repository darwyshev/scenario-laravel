<!DOCTYPE html>
<html>
<head>
    <title>Landing</title>
</head>
<body>
<h2>Ini Halaman Landing Page</h2>

<a href="{{ url('/login') }}"><button>Login disini</button></a>
<a href="{{ url('/register') }}"><button>Register disini</button></a>

@foreach($konten as $k => $data)
    <a href="{{ url('/detil/' . $data->id) }}">
        <h1>{{ $data->id }} {{ $data->judul }}</h1>
    </a>
    <p>{{ $data->isi }}</p>
    <br>
@endforeach

</body>
</html>
