@extends('layouts.app')

@section('title', 'Beranda - Sistem Absensi Sekolah')

@section('content')
<div class="mobile-grid">
    <!-- Hero Section -->
    <div class="mobile-card animate-fade-in">
        <div class="mobile-padding text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-black rounded-full mb-4 sm:mb-6 mx-auto">
                <i class="bi bi-calendar-check text-white mobile-icon-lg sm:text-3xl"></i>
            </div>
            <h1 class="mobile-text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">Sistem Absensi Sekolah Digital</h1>
            <p class="text-gray-600 leading-relaxed mobile-text-sm">
                Sistem absensi inovatif berbasis lokasi GPS, foto selfie, dan validasi waktu real-time.
                Absensi hanya dapat dilakukan dalam radius sekolah dan pada jam yang telah ditentukan.
            </p>
        </div>

        <!-- School Info Card -->
        @if($school)
        <div class="mt-6 sm:mt-8 bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-6 mx-3 sm:mx-4">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-lg sm:rounded-xl flex items-center justify-center border border-gray-300">
                        <i class="bi bi-building text-gray-700 mobile-icon-md sm:text-xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="mobile-text-lg sm:text-lg font-semibold text-gray-900 mb-2">{{ $school->name }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4">
                        <div class="flex items-center gap-2 mobile-text-xs sm:text-sm">
                            <i class="bi bi-geo-alt text-gray-500"></i>
                            <span class="text-gray-600">Lokasi</span>
                        </div>
                        <div class="flex items-center gap-2 mobile-text-xs sm:text-sm">
                            <i class="bi bi-crop text-gray-500"></i>
                            <span class="text-gray-600">Radius: <strong class="font-semibold">{{ $school->radius }}m</strong></span>
                        </div>
                        <div class="flex items-center gap-2 mobile-text-xs sm:text-sm">
                            <i class="bi bi-clock text-gray-500"></i>
                            <span class="text-gray-600">Waktu: <strong class="font-semibold text-green-600">Aktif</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Status Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mt-6 sm:mt-8">
            <!-- Time Status Card -->
            <div class="mobile-card hover-lift" id="timeInfoAlert">
                <div class="mobile-padding">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center bg-gray-50 border border-gray-200" id="timeIconContainer">
                            <i class="bi bi-clock-history text-gray-700 mobile-icon-md sm:text-xl" id="timeIcon"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mobile-text-sm sm:text-base truncate" id="timeInfoTitle">Memuat waktu...</h3>
                        </div>
                    </div>
                    <div class="space-y-2" id="timeInfoContent">
                        <div class="flex items-center justify-center py-2">
                            <div class="animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-2 border-gray-300 border-t-gray-700"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distance Card -->
            <div class="mobile-card hover-lift">
                <div class="mobile-padding">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center bg-gray-50 border border-gray-200">
                            <i class="bi bi-compass text-gray-700 mobile-icon-md sm:text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mobile-text-sm sm:text-base">Jarak</h3>
                            <p class="mobile-text-xs text-gray-500 truncate">Real-time tracking</p>
                        </div>
                    </div>
                    <div class="text-center py-3 sm:py-4">
                        <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-1 sm:mb-2" id="distanceValue">- -</div>
                        <div class="mobile-text-xs text-gray-500 font-medium">meter</div>
                    </div>
                    <div class="mt-3 sm:mt-4 text-center" id="attendanceStatus">
                        <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full mobile-text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                            <span class="status-dot bg-yellow-500"></span>
                            Menunggu lokasi
                        </span>
                    </div>
                    <div class="mt-3 sm:mt-4 text-center">
                        <small class="text-gray-500 mobile-text-xs font-medium truncate block" id="locationStatus">
                            Mengaktifkan GPS...
                        </small>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="mobile-card hover-lift">
                <div class="mobile-padding">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center bg-gray-50 border border-gray-200">
                            <i class="bi bi-shield-check text-gray-700 mobile-icon-md sm:text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mobile-text-sm sm:text-base">Status Sistem</h3>
                            <p class="mobile-text-xs text-gray-500 truncate">Verifikasi multi-layer</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-1 sm:py-2 border-b border-gray-100">
                            <span class="mobile-text-sm text-gray-600 font-medium">Lokasi</span>
                            <span class="mobile-text-xs font-medium" id="locationStatusBadge">-</span>
                        </div>
                        <div class="flex items-center justify-between py-1 sm:py-2 border-b border-gray-100">
                            <span class="mobile-text-sm text-gray-600 font-medium">Waktu</span>
                            <span class="mobile-text-xs font-medium" id="timeStatusBadge">-</span>
                        </div>
                        <div class="flex items-center justify-between py-1 sm:py-2">
                            <span class="mobile-text-sm text-gray-600 font-medium">Foto Selfie</span>
                            <span class="mobile-text-xs font-medium" id="photoStatusBadge">Belum</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login/User Section -->
    @if(!session()->has('user_id'))
    <!-- Login Section -->
    <div class="mobile-card animate-fade-in">
        <div class="mobile-padding text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-black rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <i class="bi bi-person-check text-white mobile-icon-lg sm:text-3xl"></i>
            </div>
            <h2 class="mobile-text-lg sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Login untuk Absensi</h2>
            <p class="text-gray-600 mb-6 sm:mb-8 leading-relaxed mobile-text-sm">
                Untuk melakukan absensi, Anda harus login terlebih dahulu dengan username, nomor HP, dan password.
            </p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 sm:gap-3 bg-black text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl hover:bg-gray-800 transition-colors mobile-text-sm sm:text-base font-semibold mobile-hover w-full justify-center">
                <i class="bi bi-box-arrow-in-right mobile-icon-md sm:text-xl"></i>
                <span>Login Sekarang</span>
            </a>
        </div>
    </div>
    @else
    <!-- User Info & Attendance Section -->
    <div class="mobile-card animate-fade-in">
        <div class="mobile-padding">
            <!-- User Info Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-8">
                <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-0">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl flex items-center justify-center bg-gray-50 border border-gray-200">
                        <i class="bi bi-person-circle mobile-text-xl sm:text-3xl text-gray-700"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="mobile-text-lg sm:text-2xl font-bold text-gray-900 truncate">Halo, {{ $fullName ?? $userName }}!</h2>
                        <div class="flex items-center gap-2 sm:gap-3 mt-1 sm:mt-2">
                            <span class="px-2 py-1 sm:px-3 sm:py-1 bg-gray-100 text-gray-700 rounded-full mobile-text-xs font-semibold border border-gray-200 truncate">
                                {{ ucfirst($userRole) }}
                            </span>
                            <span class="text-gray-500 mobile-text-xs truncate">ID: {{ session('user_id') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($hasAttendedToday)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <i class="bi bi-check-circle text-yellow-600 mobile-icon-md"></i>
                        <div class="flex-1 min-w-0">
                            <h4 class="mobile-text-sm font-semibold text-yellow-800 truncate">Sudah Absen Hari Ini</h4>
                            <p class="text-yellow-700 mobile-text-xs truncate">Absensi lagi besok</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if(!$hasAttendedToday)
            <!-- Selfie Camera Section -->
            <div class="mb-6 sm:mb-8" id="selfieSection" style="display: none;">
                <div class="text-center mb-4 sm:mb-6">
                    <h3 class="mobile-text-lg sm:text-xl font-semibold text-gray-900 mb-2">
                        <i class="bi bi-camera mr-2"></i> Ambil Foto Selfie
                    </h3>
                    <p class="text-gray-600 mobile-text-sm">Foto selfie akan digunakan untuk validasi kehadiran Anda.</p>
                </div>
                
                <div class="mobile-grid">
                    <!-- Camera Preview with Controls -->
                    <div class="camera-container">
                        <video id="video" class="w-full h-auto" autoplay playsinline></video>
                        <div class="camera-controls">
                            <button type="button" class="camera-btn" id="toggleCameraBtn" title="Matikan Kamera">
                                <i class="bi bi-camera-video-off" id="cameraToggleIcon"></i>
                                <span class="hidden sm:inline">Matikan</span>
                            </button>
                            <button type="button" class="camera-btn" id="captureBtn" title="Ambil Foto">
                                <i class="bi bi-camera-fill"></i>
                                <span class="hidden sm:inline">Ambil Foto</span>
                            </button>
                            <button type="button" class="camera-btn bg-red-500" id="stopCameraBtn" title="Stop Kamera" style="display: none;">
                                <i class="bi bi-power"></i>
                                <span class="hidden sm:inline">Stop</span>
                            </button>
                        </div>
                        <canvas id="canvas" width="400" height="300" style="display: none;"></canvas>
                    </div>
                    
                    <!-- Captured Image Preview -->
                    <div id="capturePreview" class="mobile-card" style="display: none;">
                        <div class="mobile-padding">
                            <h4 class="font-semibold text-gray-900 mb-3 sm:mb-4 mobile-text-sm">Preview Foto:</h4>
                            <div class="flex justify-center mb-3 sm:mb-4">
                                <img id="photoPreview" src="#" alt="Foto Preview" class="w-32 h-32 sm:w-48 sm:h-48 object-cover rounded-lg sm:rounded-xl border-2 border-white shadow">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                                <button type="button" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors mobile-text-sm font-medium mobile-hover" id="retakeBtn">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    <span>Ambil Ulang</span>
                                </button>
                                <button type="button" class="flex-1 flex items-center justify-center gap-2 bg-black text-white px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors mobile-text-sm font-medium mobile-hover" id="usePhotoBtn">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Gunakan Foto</span>
                                </button>
                            </div>
                            <p class="text-center text-gray-500 mobile-text-xs mt-3">Foto ini akan digunakan untuk validasi kehadiran</p>
                        </div>
                    </div>
                    
                    <!-- Camera Info -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                        <div class="flex items-start gap-2 sm:gap-3">
                            <i class="bi bi-info-circle text-gray-600 mt-0.5 mobile-icon-sm"></i>
                            <div>
                                <p class="text-gray-700 mobile-text-xs sm:text-sm">
                                    <strong class="font-semibold">Pastikan:</strong> Wajah terlihat jelas dan pencahayaan cukup.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Error Message -->
                    <div id="cameraError" class="bg-red-50 border border-red-200 rounded-lg sm:rounded-xl p-3 sm:p-4" style="display: none;">
                        <div class="flex items-start gap-2 sm:gap-3">
                            <i class="bi bi-exclamation-triangle text-red-600 mobile-icon-sm"></i>
                            <div>
                                <h4 class="font-semibold text-red-800 mobile-text-sm mb-1">Kamera Tidak Tersedia</h4>
                                <p class="text-red-700 mobile-text-xs">
                                    Silakan gunakan browser lain atau izinkan akses kamera.
                                    <button type="button" class="text-blue-600 underline ml-1" onclick="retryCamera()">Coba lagi</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Form -->
            <form id="attendanceForm" action="{{ route('hadir') }}" method="POST">
                @csrf
                <input type="hidden" name="latitude" id="attendanceLat">
                <input type="hidden" name="longitude" id="attendanceLng">
                <input type="hidden" name="distance" id="attendanceDistance">
                <input type="hidden" name="selfie_photo" id="selfiePhotoInput">
                
                <div class="text-center">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-black text-white px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl hover:bg-gray-800 transition-colors mobile-text-base sm:text-lg font-bold mobile-hover disabled:opacity-50 disabled:cursor-not-allowed" id="attendanceBtn" disabled>
                        <i class="bi bi-check-circle mobile-icon-lg sm:text-2xl"></i>
                        <span class="truncate">HADIR DENGAN FOTO</span>
                    </button>
                    
                    <div class="mt-3 sm:mt-4" id="attendanceMessage">
                        <div class="flex items-center justify-center gap-2 sm:gap-3 text-gray-600">
                            <div class="animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-2 border-gray-300 border-t-gray-700" id="timeCheckSpinner"></div>
                            <span class="mobile-text-xs sm:text-sm font-medium truncate" id="attendanceMessageText">Memeriksa kondisi absensi...</span>
                        </div>
                    </div>
                </div>
            </form>
            
            @endif
            
            @if($userRole === 'guru')
            <div class="mt-6 sm:mt-8 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                <div class="flex items-center gap-2 sm:gap-4">
                    <i class="bi bi-info-circle text-gray-600 mobile-icon-md"></i>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1 mobile-text-sm">Informasi Guru</h4>
                        <p class="text-gray-700 mobile-text-xs">Guru wajib melakukan absensi dengan foto selfie seperti murid.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- System Features -->
    <div class="mobile-card animate-fade-in">
        <div class="mobile-padding">
            <h5 class="mobile-text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 text-center">
                <i class="bi bi-star-fill mr-2"></i> Fitur Sistem
            </h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                @php
                    $features = [
                        ['icon' => 'bi-geo-alt', 'title' => 'Validasi GPS', 'desc' => 'Absensi hanya dalam radius sekolah'],
                        ['icon' => 'bi-clock', 'title' => 'Validasi Waktu', 'desc' => 'Hanya pada jam yang ditentukan'],
                        ['icon' => 'bi-camera', 'title' => 'Foto Selfie', 'desc' => 'Validasi kehadiran dengan foto'],
                        ['icon' => 'bi-shield-check', 'title' => 'Keamanan', 'desc' => 'Multi-layer security system'],
                        ['icon' => 'bi-people', 'title' => 'Multi-Role', 'desc' => 'Murid, Guru, dan Admin'],
                        ['icon' => 'bi-graph-up', 'title' => 'Analytics', 'desc' => 'Monitoring real-time']
                    ];
                @endphp
                
                @foreach($features as $feature)
                    <div class="mobile-card hover-lift">
                        <div class="mobile-padding">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-50 border border-gray-200">
                                    <i class="bi {{ $feature['icon'] }} text-gray-700 mobile-icon-md"></i>
                                </div>
                                <h6 class="font-semibold text-gray-900 mobile-text-sm">{{ $feature['title'] }}</h6>
                            </div>
                            <p class="text-gray-600 mobile-text-xs leading-relaxed">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="mobile-card animate-fade-in">
        <div class="mobile-padding">
            <h5 class="mobile-text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 text-center">
                <i class="bi bi-lightbulb mr-2"></i> Cara Kerja
            </h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-4">
                        <span class="text-gray-900 font-bold mobile-text-lg sm:text-xl">1</span>
                    </div>
                    <h6 class="font-semibold text-gray-900 mb-1 mobile-text-sm">Login</h6>
                    <p class="text-gray-600 mobile-text-xs">Masuk dengan akun</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-4">
                        <span class="text-gray-900 font-bold mobile-text-lg sm:text-xl">2</span>
                    </div>
                    <h6 class="font-semibold text-gray-900 mb-1 mobile-text-sm">Ambil Foto</h6>
                    <p class="text-gray-600 mobile-text-xs">Foto selfie untuk validasi</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-4">
                        <span class="text-gray-900 font-bold mobile-text-lg sm:text-xl">3</span>
                    </div>
                    <h6 class="font-semibold text-gray-900 mb-1 mobile-text-sm">Verifikasi</h6>
                    <p class="text-gray-600 mobile-text-xs">Sistem verifikasi lokasi & waktu</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-4">
                        <span class="text-gray-900 font-bold mobile-text-lg sm:text-xl">4</span>
                    </div>
                    <h6 class="font-semibold text-gray-900 mb-1 mobile-text-sm">Selesai</h6>
                    <p class="text-gray-600 mobile-text-xs">Absensi berhasil dicatat</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Control Buttons -->
    <div class="text-center">
        <div class="inline-flex flex-col sm:flex-row gap-2 sm:gap-4">
            <button onclick="retryLocation()" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors mobile-text-sm font-medium mobile-hover">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh Lokasi</span>
            </button>
            <button onclick="loadTimeSettings()" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors mobile-text-sm font-medium mobile-hover">
                <i class="bi bi-clock"></i>
                <span>Refresh Waktu</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ============================================
    // KAMERA CONTROL FUNCTIONS
    // ============================================
    
    let watchId = null;
    let currentDistance = null;
    let isWithinRadius = false;
    let videoStream = null;
    let photoData = null;
    let isAllowedTime = false;
    let timeMessage = 'Memeriksa waktu...';
    let activeTimeSettings = [];
    let serverTimezone = 'Loading...';
    let isCameraActive = false;
    let isCameraPaused = false;

    // Camera functions with controls
    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError('Browser tidak mendukung akses kamera');
            return;
        }

        // Show loading state
        const selfieSection = document.getElementById('selfieSection');
        selfieSection.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-300 border-t-black mx-auto mb-4"></div>
                <p class="text-gray-600 mobile-text-sm">Mengaktifkan kamera...</p>
            </div>
        `;

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'user',
                frameRate: { ideal: 30 }
            },
            audio: false
        })
        .then(function(stream) {
            videoStream = stream;
            isCameraActive = true;
            
            // Show camera UI
            selfieSection.innerHTML = `
                <div class="text-center mb-4 sm:mb-6">
                    <h3 class="mobile-text-lg sm:text-xl font-semibold text-gray-900 mb-2">
                        <i class="bi bi-camera mr-2"></i> Ambil Foto Selfie
                    </h3>
                    <p class="text-gray-600 mobile-text-sm">Foto selfie akan digunakan untuk validasi kehadiran Anda.</p>
                </div>
                
                <div class="mobile-grid">
                    <div class="camera-container">
                        <video id="video" class="w-full h-auto" autoplay playsinline></video>
                        <div class="camera-controls">
                            <button type="button" class="camera-btn" id="toggleCameraBtn" title="${isCameraPaused ? 'Nyalakan Kamera' : 'Matikan Kamera'}">
                                <i class="bi ${isCameraPaused ? 'bi-camera-video' : 'bi-camera-video-off'}" id="cameraToggleIcon"></i>
                                <span class="hidden sm:inline">${isCameraPaused ? 'Nyalakan' : 'Matikan'}</span>
                            </button>
                            <button type="button" class="camera-btn" id="captureBtn" title="Ambil Foto">
                                <i class="bi bi-camera-fill"></i>
                                <span class="hidden sm:inline">Ambil Foto</span>
                            </button>
                            <button type="button" class="camera-btn bg-red-500" id="stopCameraBtn" title="Stop Kamera">
                                <i class="bi bi-power"></i>
                                <span class="hidden sm:inline">Stop</span>
                            </button>
                        </div>
                        <canvas id="canvas" width="400" height="300" style="display: none;"></canvas>
                    </div>
                    
                    <div id="capturePreview" class="mobile-card" style="display: none;">
                        <div class="mobile-padding">
                            <h4 class="font-semibold text-gray-900 mb-3 sm:mb-4 mobile-text-sm">Preview Foto:</h4>
                            <div class="flex justify-center mb-3 sm:mb-4">
                                <img id="photoPreview" src="#" alt="Foto Preview" class="w-32 h-32 sm:w-48 sm:h-48 object-cover rounded-lg sm:rounded-xl border-2 border-white shadow">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                                <button type="button" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors mobile-text-sm font-medium mobile-hover" id="retakeBtn">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    <span>Ambil Ulang</span>
                                </button>
                                <button type="button" class="flex-1 flex items-center justify-center gap-2 bg-black text-white px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors mobile-text-sm font-medium mobile-hover" id="usePhotoBtn">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Gunakan Foto</span>
                                </button>
                            </div>
                            <p class="text-center text-gray-500 mobile-text-xs mt-3">Foto ini akan digunakan untuk validasi kehadiran</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                        <div class="flex items-start gap-2 sm:gap-3">
                            <i class="bi bi-info-circle text-gray-600 mt-0.5 mobile-icon-sm"></i>
                            <div>
                                <p class="text-gray-700 mobile-text-xs sm:text-sm">
                                    <strong class="font-semibold">Pastikan:</strong> Wajah terlihat jelas dan pencahayaan cukup.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Set up video element
            const video = document.getElementById('video');
            video.srcObject = stream;
            video.play().catch(e => {
                console.error("Error playing video:", e);
                showCameraError('Tidak dapat memutar video');
            });
            
            // Show camera section
            selfieSection.style.display = 'block';
            
            // Setup camera controls
            setupCameraControls();
            
        })
        .catch(function(err) {
            console.error("Error accessing camera: ", err);
            
            let errorMessage = 'Kamera tidak dapat diakses';
            if (err.name === 'NotAllowedError') {
                errorMessage = 'Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan browser.';
            } else if (err.name === 'NotFoundError') {
                errorMessage = 'Kamera tidak ditemukan pada perangkat ini.';
            } else if (err.name === 'NotReadableError') {
                errorMessage = 'Kamera sedang digunakan oleh aplikasi lain.';
            }
            
            showCameraError(errorMessage);
        });
    }

    function setupCameraControls() {
        // Toggle camera (pause/resume)
        document.getElementById('toggleCameraBtn')?.addEventListener('click', function() {
            const video = document.getElementById('video');
            const icon = document.getElementById('cameraToggleIcon');
            const button = document.getElementById('toggleCameraBtn');
            
            if (isCameraPaused) {
                // Resume camera
                if (videoStream) {
                    video.srcObject = videoStream;
                    video.play();
                }
                icon.className = 'bi bi-camera-video-off';
                button.innerHTML = `<i class="bi bi-camera-video-off" id="cameraToggleIcon"></i><span class="hidden sm:inline">Matikan</span>`;
                isCameraPaused = false;
            } else {
                // Pause camera
                video.srcObject = null;
                icon.className = 'bi bi-camera-video';
                button.innerHTML = `<i class="bi bi-camera-video" id="cameraToggleIcon"></i><span class="hidden sm:inline">Nyalakan</span>`;
                isCameraPaused = true;
            }
        });
        
        // Stop camera completely
        document.getElementById('stopCameraBtn')?.addEventListener('click', function() {
            stopCamera();
            document.getElementById('selfieSection').style.display = 'none';
            alert('Kamera telah dimatikan. Klik tombol "Ambil Foto Selfie" untuk mengaktifkan kembali.');
        });
        
        // Capture photo
        document.getElementById('captureBtn')?.addEventListener('click', capturePhoto);
        
        // Retake photo
        document.getElementById('retakeBtn')?.addEventListener('click', retakePhoto);
        
        // Use photo
        document.getElementById('usePhotoBtn')?.addEventListener('click', function() {
            if (photoData) {
                document.getElementById('selfiePhotoInput').value = photoData;
                updateAttendanceButton();
                updateStatusBadges();
                alert('Foto berhasil dipilih! Anda dapat melanjutkan absensi.');
            }
        });
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        
        const video = document.getElementById('video');
        if (video) {
            video.srcObject = null;
        }
        
        isCameraActive = false;
        isCameraPaused = false;
    }

    function showCameraError(message) {
        const selfieSection = document.getElementById('selfieSection');
        selfieSection.innerHTML = `
            <div class="mobile-card">
                <div class="mobile-padding">
                    <div class="text-center mb-4">
                        <i class="bi bi-camera-video-off text-red-500 text-4xl mb-3"></i>
                        <h3 class="mobile-text-lg font-semibold text-gray-900 mb-2">Kamera Tidak Tersedia</h3>
                        <p class="text-gray-600 mobile-text-sm mb-4">${message}</p>
                    </div>
                    
                    <div class="space-y-3">
                        <button onclick="retryCamera()" class="w-full flex items-center justify-center gap-2 bg-black text-white px-4 py-3 rounded-lg mobile-text-sm font-medium mobile-hover">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span>Coba Lagi</span>
                        </button>
                        
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-gray-700 mobile-text-xs mb-2"><strong>Alternatif:</strong> Anda dapat menggunakan foto dari galeri:</p>
                            <input type="file" id="fileInput" accept="image/*" capture="environment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        selfieSection.style.display = 'block';
        
        // Setup file upload
        document.getElementById('fileInput')?.addEventListener('change', handleFileUpload);
    }

    function retryCamera() {
        startCamera();
    }

    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        
        if (!video || !canvas) {
            alert('Elemen video atau canvas tidak ditemukan');
            return;
        }
        
        if (isCameraPaused) {
            alert('Nyalakan kamera terlebih dahulu untuk mengambil foto');
            return;
        }
        
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw current video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to data URL
        photoData = canvas.toDataURL('image/jpeg', 0.85);
        
        // Show preview
        document.getElementById('photoPreview').src = photoData;
        document.getElementById('capturePreview').style.display = 'block';
        
        // Pause video after capture
        video.pause();
        isCameraPaused = true;
        document.getElementById('cameraToggleIcon').className = 'bi bi-camera-video';
        document.getElementById('toggleCameraBtn').innerHTML = `<i class="bi bi-camera-video" id="cameraToggleIcon"></i><span class="hidden sm:inline">Nyalakan</span>`;
        
        updateAttendanceButton();
        updateStatusBadges();
    }

    function retakePhoto() {
        document.getElementById('capturePreview').style.display = 'none';
        photoData = null;
        
        // Resume video
        const video = document.getElementById('video');
        if (video && videoStream) {
            video.srcObject = videoStream;
            video.play();
            isCameraPaused = false;
            document.getElementById('cameraToggleIcon').className = 'bi bi-camera-video-off';
            document.getElementById('toggleCameraBtn').innerHTML = `<i class="bi bi-camera-video-off" id="cameraToggleIcon"></i><span class="hidden sm:inline">Matikan</span>`;
        }
        
        updateAttendanceButton();
        updateStatusBadges();
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Hanya file gambar yang diizinkan');
                return;
            }
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                photoData = e.target.result;
                document.getElementById('selfiePhotoInput').value = photoData;
                
                // Show preview
                document.getElementById('photoPreview').src = photoData;
                document.getElementById('capturePreview').style.display = 'block';
                
                updateAttendanceButton();
                updateStatusBadges();
                alert('Foto berhasil diunggah!');
            };
            reader.readAsDataURL(file);
        }
    }

    // Rest of the existing JavaScript functions remain the same...
    // [All the existing JavaScript functions from previous code - loadTimeSettings, updateAttendanceButton, getLocation, etc.]
    // I'll keep the structure but update for mobile responsiveness

    function loadTimeSettings() {
        console.log('Memuat pengaturan waktu dari server...');
        
        // Show loading state
        const spinner = document.getElementById('timeCheckSpinner');
        if (spinner) spinner.style.display = 'block';
        
        $.ajax({
            url: '{{ route("check.attendance.time") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('✅ Response waktu dari server:', response);
                
                isAllowedTime = response.allowed;
                timeMessage = response.message;
                activeTimeSettings = response.settings || [];
                
                updateTimeInfoDisplay(response);
                updateAttendanceButton();
                updateStatusBadges();
                
                if (spinner) spinner.style.display = 'none';
            },
            error: function(xhr, status, error) {
                console.error('❌ Gagal memuat pengaturan waktu:', error);
                
                // Fallback untuk testing
                const now = new Date();
                const hour = now.getHours().toString().padStart(2, '0');
                const minute = now.getMinutes().toString().padStart(2, '0');
                
                const isTestingAllowed = (hour >= 7 && hour < 16);
                
                isAllowedTime = isTestingAllowed;
                timeMessage = isTestingAllowed ? 
                    'Waktu diizinkan (07:00-16:00)' : 
                    'Waktu tidak diizinkan (07:00-16:00)';
                activeTimeSettings = [];
                
                updateTimeInfoDisplay({
                    allowed: isTestingAllowed,
                    message: timeMessage,
                    current_hour_minute: hour + ':' + minute,
                    current_day_name: now.toLocaleDateString('id-ID', {weekday: 'long'})
                });
                updateAttendanceButton();
                updateStatusBadges();
                
                if (spinner) spinner.style.display = 'none';
            }
        });
    }

    function updateTimeInfoDisplay(response) {
        const timeAlert = document.getElementById('timeInfoAlert');
        const timeIconContainer = document.getElementById('timeIconContainer');
        const timeIcon = document.getElementById('timeIcon');
        const timeTitle = document.getElementById('timeInfoTitle');
        const timeContent = document.getElementById('timeInfoContent');
        
        if (!timeAlert || !timeIconContainer || !timeIcon || !timeTitle || !timeContent) return;
        
        if (isAllowedTime) {
            timeAlert.className = 'mobile-card hover-lift border border-green-200';
            timeIconContainer.className = 'w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center bg-green-50 border border-green-200';
            timeIcon.className = 'bi bi-check-circle text-green-600 mobile-icon-md sm:text-xl';
            timeTitle.innerHTML = '<i class="bi bi-check-circle text-green-600"></i> Waktu DIJINKAN';
            
            let settingsHtml = '';
            if (activeTimeSettings && activeTimeSettings.length > 0) {
                settingsHtml = '<div class="mt-2 sm:mt-4"><small class="text-gray-600 mobile-text-xs font-medium">Waktu yang diizinkan:</small><div class="flex flex-wrap gap-1 sm:gap-2 mt-1 sm:mt-2">';
                activeTimeSettings.forEach(setting => {
                    settingsHtml += `<span class="px-2 py-1 bg-green-50 text-green-700 rounded-full mobile-text-xs font-medium border border-green-200">
                        ${setting.time_range}
                    </span>`;
                });
                settingsHtml += '</div></div>';
            }
            
            timeContent.innerHTML = `
                <div class="text-green-700 font-medium mobile-text-sm">${timeMessage}</div>
                <div class="mobile-text-xs text-gray-600 mt-1 sm:mt-2 space-y-1">
                    <div>Waktu: ${response.current_hour_minute || ''}</div>
                    <div>Hari: ${response.current_day_name || ''}</div>
                </div>
                ${settingsHtml}
            `;
        } else {
            timeAlert.className = 'mobile-card hover-lift border border-red-200';
            timeIconContainer.className = 'w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center bg-red-50 border border-red-200';
            timeIcon.className = 'bi bi-x-circle text-red-600 mobile-icon-md sm:text-xl';
            timeTitle.innerHTML = '<i class="bi bi-x-circle text-red-600"></i> Waktu TIDAK DIJINKAN';
            
            let settingsHtml = '';
            if (activeTimeSettings && activeTimeSettings.length > 0) {
                settingsHtml = '<div class="mt-2 sm:mt-4"><small class="text-gray-600 mobile-text-xs font-medium"><strong>Waktu absen yang diizinkan:</strong></small><ul class="mt-1 sm:mt-2 space-y-1">';
                activeTimeSettings.forEach(setting => {
                    settingsHtml += `<li class="mobile-text-xs text-gray-700">• ${setting.time_range}</li>`;
                });
                settingsHtml += '</ul></div>';
            }
            
            timeContent.innerHTML = `
                <div class="text-red-700 font-medium mobile-text-sm">${timeMessage}</div>
                <div class="mobile-text-xs text-gray-600 mt-1 sm:mt-2 space-y-1">
                    <div>Waktu: ${response.current_hour_minute || ''}</div>
                    <div>Hari: ${response.current_day_name || ''}</div>
                </div>
                ${settingsHtml}
            `;
        }
    }

    function updateAttendanceButton() {
        const attendanceBtn = document.getElementById('attendanceBtn');
        const attendanceMessage = document.getElementById('attendanceMessageText');
        const timeSpinner = document.getElementById('timeCheckSpinner');
        
        if (!attendanceBtn || !attendanceMessage) return;
        
        if (isWithinRadius && photoData && isAllowedTime) {
            attendanceBtn.disabled = false;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-black text-white px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl hover:bg-gray-800 transition-colors mobile-text-base sm:text-lg font-bold mobile-hover disabled:opacity-50 disabled:cursor-not-allowed';
            attendanceMessage.innerHTML = `
                <i class="bi bi-check-circle text-green-600"></i> 
                <span class="text-green-700 font-medium">Siap absensi</span>
                <br class="hidden sm:block">
                <small class="text-gray-500 mobile-text-xs hidden sm:inline">${timeMessage}</small>
            `;
            if (timeSpinner) timeSpinner.style.display = 'none';
        } else if (!isAllowedTime) {
            attendanceBtn.disabled = true;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-gray-100 text-gray-500 px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl mobile-text-base sm:text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed';
            attendanceMessage.innerHTML = `
                <i class="bi bi-clock text-yellow-600"></i> 
                <span class="text-yellow-700 font-medium">${timeMessage}</span>
                <br class="hidden sm:block">
                <small class="text-gray-500 mobile-text-xs hidden sm:inline">Hanya pada jam tertentu</small>
            `;
            if (timeSpinner) timeSpinner.style.display = 'none';
        } else if (isWithinRadius && !photoData) {
            attendanceBtn.disabled = true;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-yellow-100 text-yellow-700 px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl mobile-text-base sm:text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed';
            attendanceMessage.innerHTML = `
                <i class="bi bi-camera text-yellow-600"></i> 
                <span class="text-yellow-700 font-medium">Ambil foto terlebih dahulu</span>
                <br class="hidden sm:block">
                <small class="text-gray-500 mobile-text-xs hidden sm:inline">${timeMessage}</small>
            `;
            if (timeSpinner) timeSpinner.style.display = 'none';
        } else if (!isWithinRadius) {
            attendanceBtn.disabled = true;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-gray-100 text-gray-500 px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl mobile-text-base sm:text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed';
            attendanceMessage.innerHTML = `
                <i class="bi bi-geo-alt text-red-600"></i> 
                <span class="text-red-700 font-medium">Luar radius sekolah</span>
                <br class="hidden sm:block">
                <small class="text-gray-500 mobile-text-xs hidden sm:inline">${timeMessage}</small>
            `;
            if (timeSpinner) timeSpinner.style.display = 'none';
        } else {
            attendanceBtn.disabled = true;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-gray-100 text-gray-500 px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl mobile-text-base sm:text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed';
            attendanceMessage.innerHTML = `
                <i class="bi bi-hourglass text-gray-600"></i> 
                <span class="text-gray-700 font-medium">Memeriksa...</span>
                <br class="hidden sm:block">
                <small class="text-gray-500 mobile-text-xs hidden sm:inline">${timeMessage}</small>
            `;
        }
    }

    function updateDistanceInfo(distance, isWithin) {
        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        const locationStatus = document.getElementById('locationStatus');
        
        let displayDistance = 'Error';
        if (distance !== null && !isNaN(distance)) {
            displayDistance = Math.round(distance) + ' m';
        }
        
        if (distanceElement) distanceElement.textContent = displayDistance;
        currentDistance = distance;
        isWithinRadius = isWithin;
        
        const attendanceDistance = document.getElementById('attendanceDistance');
        if (attendanceDistance) attendanceDistance.value = distance || 0;
        
        if (isWithin) {
            if (statusElement) statusElement.innerHTML = '<span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full mobile-text-xs font-medium bg-green-50 text-green-700 border border-green-200"><span class="status-dot bg-green-500"></span>Dalam Radius</span>';
            if (locationStatus) {
                locationStatus.innerHTML = '<i class="bi bi-check-circle text-green-600"></i> Dalam radius sekolah';
                locationStatus.className = 'text-green-600 mobile-text-xs font-medium truncate block';
            }
        } else {
            if (statusElement) statusElement.innerHTML = '<span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full mobile-text-xs font-medium bg-red-50 text-red-700 border border-red-200"><span class="status-dot bg-red-500"></span>Luar Radius</span>';
            if (locationStatus) {
                locationStatus.innerHTML = '<i class="bi bi-exclamation-circle text-red-600"></i> Luar radius sekolah';
                locationStatus.className = 'text-red-600 mobile-text-xs font-medium truncate block';
            }
        }
        
        updateAttendanceButton();
        updateStatusBadges();
    }

    function getLocation() {
        if (!navigator.geolocation) {
            showError('Browser tidak mendukung GPS');
            return;
        }

        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        
        if (distanceElement) distanceElement.textContent = 'Mendeteksi...';
        if (statusElement) statusElement.innerHTML = '<span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full mobile-text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"><span class="status-dot bg-blue-500"></span>Mengambil lokasi...</span>';
        
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                const attendanceLat = document.getElementById('attendanceLat');
                const attendanceLng = document.getElementById('attendanceLng');
                if (attendanceLat) attendanceLat.value = lat;
                if (attendanceLng) attendanceLng.value = lng;
                
                $.ajax({
                    url: '{{ route("get.distance") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        latitude: lat,
                        longitude: lng
                    },
                    success: function(response) {
                        updateDistanceInfo(response.distance, response.is_within_radius);
                    },
                    error: function() {
                        const schoolLat = {{ $school->latitude ?? -6.8632300 }};
                        const schoolLng = {{ $school->longitude ?? 108.0491849 }};
                        const distance = calculateDistance(lat, lng, schoolLat, schoolLng);
                        const radius = {{ $school->radius ?? 100 }};
                        updateDistanceInfo(distance, distance <= radius);
                    }
                });
            },
            (error) => {
                let message = 'Tidak dapat mengambil lokasi';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Izin lokasi ditolak';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'GPS tidak aktif atau sinyal lemah';
                        break;
                    case error.TIMEOUT:
                        message = 'Waktu permintaan habis';
                        break;
                }
                showError(message);
            },
            options
        );
    }

    function showError(message) {
        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        const locationStatus = document.getElementById('locationStatus');
        
        if (distanceElement) distanceElement.textContent = 'Error';
        if (statusElement) statusElement.innerHTML = '<span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full mobile-text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200"><span class="status-dot bg-yellow-500"></span>Error Lokasi</span>';
        if (locationStatus) {
            locationStatus.innerHTML = `<i class="bi bi-exclamation-triangle text-yellow-600"></i> ${message}`;
            locationStatus.className = 'text-yellow-600 mobile-text-xs font-medium truncate block';
        }
        
        const attendanceBtn = document.getElementById('attendanceBtn');
        if (attendanceBtn) {
            attendanceBtn.disabled = true;
            attendanceBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 bg-gray-100 text-gray-500 px-6 sm:px-10 py-3 sm:py-5 rounded-lg sm:rounded-2xl mobile-text-base sm:text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed';
        }
        
        updateStatusBadges();
    }

    function retryLocation() {
        getLocation();
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    }

    function updateStatusBadges() {
        // Location status
        const locationBadge = document.getElementById('locationStatusBadge');
        if (locationBadge) {
            if (isWithinRadius) {
                locationBadge.innerHTML = '<span class="inline-flex items-center text-green-600 font-medium mobile-text-xs"><span class="status-dot bg-green-500"></span>OK</span>';
            } else {
                locationBadge.innerHTML = '<span class="inline-flex items-center text-red-600 font-medium mobile-text-xs"><span class="status-dot bg-red-500"></span>Luar</span>';
            }
        }
        
        // Time status
        const timeBadge = document.getElementById('timeStatusBadge');
        if (timeBadge) {
            if (isAllowedTime) {
                timeBadge.innerHTML = '<span class="inline-flex items-center text-green-600 font-medium mobile-text-xs"><span class="status-dot bg-green-500"></span>OK</span>';
            } else {
                timeBadge.innerHTML = '<span class="inline-flex items-center text-red-600 font-medium mobile-text-xs"><span class="status-dot bg-red-500"></span>Tidak</span>';
            }
        }
        
        // Photo status
        const photoBadge = document.getElementById('photoStatusBadge');
        if (photoBadge) {
            if (photoData) {
                photoBadge.innerHTML = '<span class="inline-flex items-center text-green-600 font-medium mobile-text-xs"><span class="status-dot bg-green-500"></span>Siap</span>';
            } else {
                photoBadge.innerHTML = '<span class="inline-flex items-center text-gray-600 font-medium mobile-text-xs"><span class="status-dot bg-gray-500"></span>Belum</span>';
            }
        }
    }

    // Initialization
    $(document).ready(function() {
        console.log('Landing page initialized with mobile responsive design');
        
        // Load initial data
        loadTimeSettings();
        
        // Get location after 1 second
        setTimeout(function() {
            getLocation();
        }, 1000);
        
        // Refresh time settings every 30 seconds
        setInterval(loadTimeSettings, 30000);
        
        // Start camera if user is logged in and hasn't attended today
        @if(session()->has('user_id') && !$hasAttendedToday)
            console.log('Starting camera for attendance...');
            startCamera();
        @endif
    });
</script>
@endpush
@endsection