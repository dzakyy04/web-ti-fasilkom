@extends('auth.layout')

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Reset Password</h4>
            <div class="nk-block-des">
                <p>Jika anda lupa password, kami akan mengirimkan link untuk mengatur ulang
                    password ke email anda.</p>
            </div>
        </div>
    </div>
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email">Email</label>
            </div>
            <div class="form-control-wrap">
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                    id="email" name="email" placeholder="Masukkan email anda" value="{{ old('email') }}">
            </div>
            @error('email')
                <div class="mt-1 small text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" id="btnSubmit" class="btn btn-lg btn-primary btn-block">
                <span>Kirim Link Reset</span>
            </button>
        </div>
        <div class="form-note-s2 text-center">
            <a href="{{ route('login.view') }}"><strong>Kembali ke login</strong></a>
        </div>
    </form>
@endsection