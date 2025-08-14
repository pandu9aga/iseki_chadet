@extends('layouts.member')

@section('content')
    <div id="home" class="page active">
        <div class="container">
            <div class="content-wrapper">
                <section class="contact-grid">
                    <div class="contact-form glass">
                        <h2>Create User</h2>
                        <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="Username_User" class="form-label">Username</label>
                                <input type="text" class="form-control" id="Username_User" name="Username_User"
                                    value="{{ old('Username_User') }}" required>
                                @error('Username_User')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="Name_User" class="form-label">Name</label>
                                <input type="text" class="form-control" id="Name_User" name="Name_User"
                                    value="{{ old('Name_User') }}" required>
                                @error('Name_User')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="Password_User" class="form-label">Password</label>
                                <input type="password" class="form-control" id="Password_User" name="Password_User"
                                    required>
                                @error('Password_User')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="cta-button">Simpan</button>
                            <button type="button" class="cta-button"
                                onclick="window.location='{{ route('user') }}'">Batal</button>
                        </form>
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
        document.getElementById('photoInput')?.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('uploadedImage');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);

                const resultElement = document.getElementById('detect');
                if (resultElement) {
                    resultElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            } else {
                preview.src = '';
            }
        });
    </script>
@endsection
