@extends('layouts.member')

@section('content')
<div id="home" class="page active">
    <div class="container">
        <div class="content-wrapper">
            <section class="contact-grid">
                <div class="contact-form glass">
                    <h2>Detect Chasis</h2>
                    <form id="uploadForm" method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="photo">Take Image</label>
                            <input type="file" name="photo" id="photoInput" accept="image/*" required>
                        </div>
                        {{-- <button type="submit" class="cta-button">Upload</button> --}}
                    </form>
                </div>

                <div class="contact-info glass" id="detect">
                    <h2>Result</h2>
                    <p>Detected Chasis Number: <span id="result"></span></p>
                    <br><hr><br>
                    <button type="button" class="cta-button" onclick="runOCR()">Check</button>
                    
                </div>
            </section>

            <section class="contact-map-section">
                <div class="contact-map glass">
                    <h2>Image</h2>
                    <div class="map-container">
                        <div class="map-placeholder">
                            <div class="map-image">
                                <img id="uploadedImage" src="{{ session('image') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('style')

@endsection

@section('script')
<script>
document.getElementById('photoInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('uploadedImage');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const resultElement = document.getElementById('detect');
        if (resultElement) {
            resultElement.scrollIntoView({ behavior: 'smooth' }); // smooth scroll
        }
    } else {
        preview.src = '';
    }
});
</script>
@endsection