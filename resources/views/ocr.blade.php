<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Paddle OCR</title>
    @vite(['resources/js/app.js'])
    <script>var Module;</script>
</head>
<body>
    <h1>Upload Gambar</h1>
    <form id="uploadForm" method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
    @csrf
        <input type="file" name="photo" id="photoInput" accept="image/*" required>
        <button type="submit">Upload</button>
    </form>

    @if(session('image'))
        <h2>Gambar:</h2>
        <img id="uploadedImage" src="{{ session('image') }}" style="max-width: 100%;">
        <button onclick="runOCR()">Deteksi Teks</button>
        <pre id="result"></pre>
    @endif
</body>
</html>
