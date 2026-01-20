@extends('layouts.app')

@section('title', 'Login - Sistem Absensi')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8 bg-gray-50">
    <div class="max-w-md w-full">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-black rounded-full mb-6">
                    <i class="bi bi-shield-lock text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Sistem Absensi</h2>
                <p class="text-gray-600">Masukkan kredensial Anda untuk mengakses sistem</p>
            </div>
            
            <!-- Error/Success Messages -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-exclamation-circle text-red-600"></i>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-check-circle text-green-600"></i>
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Login Form -->
            <form action="{{ url('/login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               required 
                               value="{{ old('username') }}"
                               placeholder="contoh: murid001, guru001, atau admin"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm transition-colors">
                        @error('username')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-phone text-gray-400"></i>
                        </div>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               required 
                               value="{{ old('phone') }}"
                               placeholder="081234567890"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm transition-colors">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nomor telepon tanpa tanda hubung (-)</p>
                </div>
                
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Masukkan password Anda"
                               class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm transition-colors">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-eye" id="passwordToggle"></i>
                        </button>
                        @error('password')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-3 bg-black hover:bg-gray-800 text-white py-3.5 rounded-lg transition-colors text-sm font-medium">
                        <i class="bi bi-box-arrow-in-right text-lg"></i>
                        <span>Login ke Sistem</span>
                    </button>
                    
                    <!-- Back to Home -->
                    <div class="mt-6 text-center">
                        <a href="/" 
                           class="inline-flex items-center gap-2 text-gray-600 hover:text-black transition-colors text-sm font-medium">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Beranda</span>
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Info Section -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-info-circle text-gray-600 text-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-sm font-semibold text-gray-900 mb-3">Informasi Login</h6>
                            <ul class="text-sm text-gray-700 space-y-2.5">
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                                    <span><strong class="font-medium">Murid & Guru:</strong> Masuk ke halaman absensi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                                    <span>Pastikan username dan nomor telepon sesuai dengan yang terdaftar</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        
            
            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-center text-xs text-gray-500">
                    &copy; {{ date('Y') }} Sistem Absensi Digital
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

// Hanya angka yang diperbolehkan untuk nomor telepon
document.getElementById('phone')?.addEventListener('input', function(e) {
    // Hapus semua karakter non-angka
    this.value = this.value.replace(/\D/g, '');
});

// Auto focus
@if($errors->has('username'))
    document.getElementById('username').focus();
@elseif($errors->has('phone'))
    document.getElementById('phone').focus();
@elseif($errors->has('password'))
    document.getElementById('password').focus();
@else
    document.getElementById('username').focus();
@endif

// Validasi sebelum submit (opsional)
document.querySelector('form').addEventListener('submit', function(e) {
    const phoneInput = document.getElementById('phone');
    const phoneValue = phoneInput.value.replace(/\D/g, '');
    
    // Pastikan nomor telepon minimal 10 digit
    if (phoneValue.length < 10) {
        e.preventDefault();
        alert('Nomor telepon harus minimal 10 digit angka');
        phoneInput.focus();
        return false;
    }
    
    // Pastikan nomor telepon maksimal 15 digit
    if (phoneValue.length > 15) {
        e.preventDefault();
        alert('Nomor telepon maksimal 15 digit angka');
        phoneInput.focus();
        return false;
    }
    
    // Update value dengan hanya angka
    phoneInput.value = phoneValue;
});
</script>

<style>
/* Hapus spinner pada input number di browser tertentu */
input[type="tel"]::-webkit-outer-spin-button,
input[type="tel"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="tel"] {
    -moz-appearance: textfield;
}
</style>
@endpush
@endsection