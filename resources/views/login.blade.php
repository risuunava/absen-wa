@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">
                    <i class="bi bi-box-arrow-in-right"></i> Login Sistem Absensi
                </h3>
                
                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               required placeholder="Masukkan username Anda">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor HP</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               required placeholder="Masukkan nomor HP Anda">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Masukkan password Anda">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Informasi Login:</h6>
                    <ul class="mb-0">
                        <li>Murid & Guru: Masuk ke halaman absensi</li>
                        <li>Admin: Masuk ke dashboard admin</li>
                        <li>Role ditentukan otomatis dari database</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection