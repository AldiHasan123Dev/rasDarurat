<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BULK UPDATE DATA RAS</title>
</head>
<body>
    @if(session('success'))
        <strong>{{ session('success') }}</strong>
    @endif
    <form action="{{ route('update.jurnal') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" id="file" accept="xlsx/csv/xls">
        <button type="submit" onclick="return confirm('are you sure?')">Upload & Update</button>
    </form>
</body>
</html>
