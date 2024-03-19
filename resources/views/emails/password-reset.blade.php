@extends('emails.layout')

@push('title')
    Reset Password
@endpush

@push('greet')
    Halo {{ $user->name }}
@endpush

@push('command')
    Klik tombol dibawah untuk melakukan reset password
@endpush

@push('button-url')
    {{ $resetUrl }}
@endpush

@push('button-name')
    Reset Password
@endpush

@push('paragraph')
    Jika anda merasa tidak melakukan permintaan ini, abaikan pesan ini. Ini
    adalah email yang dibuat secara otomatis, tolong jangan balas email ini.
@endpush

@push('button-alias')
    Reset Password
@endpush

@push('link-href')
    {{ $resetUrl }}
@endpush

@push('link-text')
    {{ $resetUrl }}
@endpush