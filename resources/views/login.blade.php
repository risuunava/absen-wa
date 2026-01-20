@extends('layouts.app')

@section('title', 'Login - Sistem Absensi')

@section('content')
<div class="max-w-md mx-auto">
    <div class="glass-card rounded-2xl p-8 animate-fade-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 gradient-bg rounded-full mb-4">
                <i class="bi bi-box-arrow-in-right text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Login Sistem Absensi</h2>
            <p class="text-gray-600 text-sm">Masukkan kredensial Anda untuk mengakses sistem</p>
        </div>
        
        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-person text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required 
                           placeholder="Masukkan username Anda"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="phone" class="text-sm font-medium text-gray-700">Nomor HP</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-phone text-gray-400"></i>
                    </div>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           required 
                           placeholder="Masukkan nomor HP Anda"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock text-gray-400"></i>
                    </div>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           placeholder="Masukkan password Anda"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                </div>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full flex items-center justify-center space-x-3 gradient-bg text-white py-4 rounded-xl hover:opacity-90 transition-opacity text-sm font-semibold hover-lift">
                    <i class="bi bi-box-arrow-in-right text-lg"></i>
                    <span>Login ke Sistem</span>
                </button>
                
                <div class="mt-4 text-center">
                    <a href="/" class="inline-flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors text-sm font-medium">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </form>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <i class="bi bi-info-circle text-blue-600 mt-0.5"></i>
                    <div class="space-y-2">
                        <h6 class="text-sm font-semibold text-blue-800">Informasi Login:</h6>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• Murid & Guru: Masuk ke halaman absensi</li>
                            <li>• Admin: Masuk ke dashboard admin</li>
                            <li>• Role ditentukan otomatis dari database</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection