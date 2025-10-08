<!DOCTYPE html>
<html>
<head>
    <title>Detail Konten</title>
</head>
<body>
    <h1>{{ $datakonten->id }} {{ $datakonten->judul }}</h1>
    <p>{{ $datakonten->isi }}</p>
    <p>{{ $datakonten->detil }}</p>

    <a href="{{ url('/') }}"><button>Back</button></a>
</body>
</html>
