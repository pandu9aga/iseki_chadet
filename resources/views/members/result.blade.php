@extends('layouts.member')

@section('content')
<div id="home" class="page active">
    <div class="container">
        <div class="content-wrapper">
            <section class="contact-grid" id="detect">
                <div class="contact-form glass">
                    <h2>Detect Chasis</h2>
                    <div class="form-group">
                        <label for="photo">Check Image</label>
                    </div>
                    <button type="button" class="cta-button" onclick="runOCR()">Check</button>
                </div>

                <div class="contact-info glass">
                    <h2>Result</h2>
                    <p>Detected Chasis Number: <span id="result"></span></p>
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

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const resultElement = document.getElementById('detect');
        if (resultElement) {
            resultElement.scrollIntoView({ behavior: 'smooth' }); // smooth scroll
        }
    });
</script>
@endsection

@section('style')
@vite(['resources/js/app.js'])
<script>var Module;</script>
@endsection