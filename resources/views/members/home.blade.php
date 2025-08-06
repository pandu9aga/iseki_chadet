@extends('layouts.member')

@section('content')
<div id="home" class="page active">
    <div class="container">
        <div class="content-wrapper">
            <section class="hero glass">
                <div class="hero-image">
                    <img src="{{ asset('assets/img/img-home.jpg') }}" alt="Detection Process Image" />
                </div>
                <div class="hero-content">
                    <h1>Welcome to the Chadet</h1>
                    <p>A website app for detecting and recording tractor chasis number.</p>
                    <a href="{{ route('detect') }}" class="cta-button">Detect Now</a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection