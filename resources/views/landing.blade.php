@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <!-- Hero Section -->
        <div class="card text-center">
            <div class="card-body">
                <h1 class="card-title display-5 mb-4">Sistem Absensi Sekolah</h1>
                <p class="card-text lead">
                    Sistem absensi berbasis lokasi untuk murid dan guru.
                    Pastikan Anda berada dalam radius sekolah untuk melakukan absensi.
                </p>
                
                @if($school)
                    <div class="alert alert-info">
                        <i class="bi bi-geo-alt"></i> 
                        Lokasi Sekolah: {{ $school->name }}<br>
                        Radius Absensi: {{ $school->radius }} meter
                    </div>
                @endif
            </div>
        </div>

        <!-- Realtime Distance Information -->
        <div class="distance-info text-center" id="distanceInfo">
            <h4><i class="bi bi-compass"></i> Jarak Anda dari Sekolah</h4>
            <div class="display-4 my-3" id="distanceValue">Mengambil lokasi...</div>
            <div id="attendanceStatus">
                <span class="badge bg-warning">Menunggu lokasi</span>
            </div>
            <small class="d-block mt-2" id="locationStatus">
                Mengaktifkan GPS untuk mendapatkan lokasi akurat
            </small>
        </div>

        <!-- Login/Attendance Section -->
        <div class="card">
            <div class="card-body text-center">
                @if(!session()->has('user_id'))
                    <!-- Not Logged In -->
                    <h4 class="card-title mb-4">Silakan Login untuk Absensi</h4>
                    <p class="card-text mb-4">
                        Untuk melakukan absensi, Anda harus login terlebih dahulu.
                        Masukkan username, nomor HP, dan password Anda.
                    </p>
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
                    </a>
                @else
                    <!-- Logged In - User Info -->
                    <div class="alert alert-success mb-4">
                        <h5><i class="bi bi-person-check"></i> Halo, {{ $fullName ?? $userName }}!</h5>
                        <p class="mb-0">Anda login sebagai <strong>{{ ucfirst($userRole) }}</strong></p>
                    </div>

                    @if($hasAttendedToday)
                        <!-- Already Attended Today -->
                        <div class="alert alert-warning">
                            <h5><i class="bi bi-check-circle"></i> Anda sudah melakukan absensi hari ini.</h5>
                            <p class="mb-0">Anda dapat melakukan absensi lagi besok.</p>
                        </div>
                    @else
                        <!-- Attendance Button -->
                        <form id="attendanceForm" action="{{ route('hadir') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="attendanceLat">
                            <input type="hidden" name="longitude" id="attendanceLng">
                            <input type="hidden" name="distance" id="attendanceDistance">
                            
                            <button type="submit" class="btn btn-success attendance-btn mb-3" id="attendanceBtn" disabled>
                                <i class="bi bi-check-circle"></i> HADIR
                            </button>
                            
                            <div id="attendanceMessage" class="text-muted">
                                Tombol akan aktif ketika Anda berada dalam radius sekolah
                            </div>
                        </form>
                    @endif
                    
                    @if($userRole === 'guru')
                        <div class="mt-3 text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Guru wajib melakukan absensi kehadiran seperti murid.
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- School Information -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle"></i> Informasi Sistem</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Fitur Utama:</strong> Absensi berbasis lokasi dengan validasi realtime
                    </li>
                    <li class="list-group-item">
                        <strong>Teknologi:</strong> Geolocation API untuk deteksi posisi
                    </li>
                    <li class="list-group-item">
                        <strong>Validasi:</strong> Absensi hanya valid dalam radius sekolah
                    </li>
                    <li class="list-group-item">
                        <strong>Role:</strong> Murid, Guru, dan Admin
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let watchId = null;
    let currentDistance = null;
    let isWithinRadius = false;

    function updateDistanceInfo(distance, isWithin) {
        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        const locationStatus = document.getElementById('locationStatus');
        const attendanceBtn = document.getElementById('attendanceBtn');
        const attendanceMessage = document.getElementById('attendanceMessage');
        
        // Format distance
        let displayDistance = 'Error';
        if (distance !== null && !isNaN(distance)) {
            displayDistance = Math.round(distance) + ' meter';
        }
        
        distanceElement.textContent = displayDistance;
        currentDistance = distance;
        isWithinRadius = isWithin;
        
        // Update hidden inputs
        document.getElementById('attendanceDistance').value = distance || 0;
        
        if (isWithin) {
            statusElement.innerHTML = '<span class="badge bg-success">Dalam Area Sekolah</span>';
            locationStatus.innerHTML = '<i class="bi bi-check-circle"></i> Anda berada dalam radius absensi';
            
            if (attendanceBtn) {
                attendanceBtn.disabled = false;
                attendanceBtn.classList.remove('btn-secondary');
                attendanceBtn.classList.add('btn-success');
                attendanceMessage.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Anda dapat melakukan absensi</span>';
            }
        } else {
            statusElement.innerHTML = '<span class="badge bg-danger">Di Luar Area Sekolah</span>';
            locationStatus.innerHTML = '<i class="bi bi-exclamation-circle"></i> Anda berada di luar radius absensi';
            
            if (attendanceBtn) {
                attendanceBtn.disabled = true;
                attendanceBtn.classList.remove('btn-success');
                attendanceBtn.classList.add('btn-secondary');
                attendanceMessage.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Anda harus berada dalam radius sekolah untuk absensi</span>';
            }
        }
    }

    function showError(message) {
        document.getElementById('distanceValue').textContent = 'Error';
        document.getElementById('attendanceStatus').innerHTML = 
            '<span class="badge bg-warning">Error Lokasi</span>';
        document.getElementById('locationStatus').innerHTML = 
            `<i class="bi bi-exclamation-triangle"></i> ${message}`;
        
        const attendanceBtn = document.getElementById('attendanceBtn');
        if (attendanceBtn) {
            attendanceBtn.disabled = true;
            attendanceBtn.classList.remove('btn-success');
            attendanceBtn.classList.add('btn-secondary');
        }
    }

    function showSuccess(message) {
        document.getElementById('locationStatus').innerHTML = 
            `<i class="bi bi-check-circle"></i> ${message}`;
    }

    function getLocation() {
        console.log('getLocation() called');
        
        if (!navigator.geolocation) {
            showError('Browser tidak mendukung Geolocation API');
            return;
        }

        // Show loading state
        document.getElementById('distanceValue').textContent = 'Mengambil lokasi...';
        document.getElementById('attendanceStatus').innerHTML = 
            '<span class="badge bg-info">Meminta izin lokasi...</span>';
        
        console.log('Requesting location permission...');

        // Options for geolocation
        const options = {
            enableHighAccuracy: true,
            timeout: 15000, // 15 detik timeout
            maximumAge: 0
        };

        // Get current position
        navigator.geolocation.getCurrentPosition(
            // Success callback
            (position) => {
                console.log('Geolocation success! Position:', position.coords);
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                console.log('Latitude:', lat, 'Longitude:', lng);
                
                // Update hidden inputs
                document.getElementById('attendanceLat').value = lat;
                document.getElementById('attendanceLng').value = lng;
                
                // Send to server to calculate distance
                $.ajax({
                    url: '{{ route("get.distance") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        latitude: lat,
                        longitude: lng
                    },
                    success: function(response) {
                        console.log('Server response:', response);
                        updateDistanceInfo(response.distance, response.is_within_radius);
                        showSuccess('Lokasi berhasil diperbarui');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        showError('Gagal menghitung jarak ke server');
                        
// Fallback: calculate distance manually
const schoolLat = {{ $school->latitude ?? -6.8632300 }};
const schoolLng = {{ $school->longitude ?? 108.0491849 }};
                        const distance = calculateDistance(lat, lng, schoolLat, schoolLng);
                        const radius = {{ $school->radius ?? 100 }};
                        updateDistanceInfo(distance, distance <= radius);
                    }
                });
            },
            // Error callback
            (error) => {
                console.error('Geolocation error:', error);
                let message = 'Tidak dapat mengambil lokasi';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Izin lokasi ditolak. Silakan: 1) Klik ikon gembok/lock di address bar, 2) Pilih "Site settings", 3) Izinkan lokasi';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'GPS tidak aktif atau sinyal lemah. Pastikan GPS diaktifkan.';
                        break;
                    case error.TIMEOUT:
                        message = 'Waktu permintaan habis. Coba refresh halaman atau pindah lokasi.';
                        break;
                    default:
                        message = 'Error: ' + error.message;
                }
                showError(message);
                
// Fallback untuk testing: gunakan lokasi sekolah
console.log('Using fallback location for testing');
const schoolLat = {{ $school->latitude ?? -6.8632300 }};
const schoolLng = {{ $school->longitude ?? 108.0491849 }};
                const distance = 50; // Simulasi 50 meter
                const radius = {{ $school->radius ?? 100 }};
                
                document.getElementById('attendanceLat').value = schoolLat;
                document.getElementById('attendanceLng').value = schoolLng;
                updateDistanceInfo(distance, distance <= radius);
            },
            options
        );

        // Watch position for updates (optional)
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
        }

        watchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('attendanceLat').value = lat;
                document.getElementById('attendanceLng').value = lng;
                
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
                    }
                });
            },
            null,
            {
                enableHighAccuracy: true,
                maximumAge: 30000,
                timeout: 10000
            }
        );
    }

    // Manual distance calculation function (fallback)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
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

    // Handle form submission
    document.getElementById('attendanceForm')?.addEventListener('submit', function(e) {
        if (!isWithinRadius) {
            e.preventDefault();
            alert('Anda harus berada dalam radius sekolah untuk melakukan absensi.');
            return;
        }
        
        if (!currentDistance) {
            e.preventDefault();
            alert('Sedang mengambil lokasi. Silakan tunggu sebentar.');
            return;
        }
        
        // Show confirmation
        if (!confirm(`Anda berada ${Math.round(currentDistance)} meter dari sekolah. Apakah Anda yakin ingin melakukan absensi?`)) {
            e.preventDefault();
        }
    });

    // Button untuk manual retry
    function retryLocation() {
        console.log('Manual retry location');
        getLocation();
    }

    // Initialize on page load
    $(document).ready(function() {
        console.log('Document ready, initializing geolocation...');
        
        // Tunggu sedikit sebelum request lokasi
        setTimeout(function() {
            getLocation();
        }, 1000);
        
        // Tambah button retry
        $('#distanceInfo').append(`
            <div class="mt-3">
                <button onclick="retryLocation()" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Lokasi
                </button>
                <button onclick="testLocation()" class="btn btn-sm btn-outline-light ms-2">
                    <i class="bi bi-geo"></i> Test Lokasi
                </button>
            </div>
        `);
    });

    // Function untuk testing lokasi
    function testLocation() {
        console.log('Testing geolocation support...');
        console.log('navigator.geolocation:', navigator.geolocation);
        
        // Test dengan lokasi dummy
const dummyLat = -6.8632300;
const dummyLng = 108.0491849;
        const distance = Math.random() * 200; // Random distance 0-200m
        
        document.getElementById('attendanceLat').value = dummyLat;
        document.getElementById('attendanceLng').value = dummyLng;
        document.getElementById('attendanceDistance').value = distance;
        
        updateDistanceInfo(distance, distance <= 100);
        showSuccess('Test lokasi berhasil (data dummy)');
    }
</script>
@endpush

{{-- Debug info --}}
@if(env('APP_DEBUG'))
<div class="mt-4">
    <details>
        <summary class="btn btn-sm btn-outline-info">Debug Info</summary>
        <div class="alert alert-warning mt-2">
            <small>
                <strong>Session Status:</strong> 
                @if(session()->has('user_id'))
                    Logged in as {{ session('user_name') }} ({{ session('user_role') }})
                @else
                    Not logged in
                @endif
                <br>
                
                <strong>School Configuration:</strong>
                @if($school)
                    <span id="schoolInfo">
                        {{ $school->name }}<br>
                        Lat: {{ $school->latitude }}, Lng: {{ $school->longitude }}, Radius: {{ $school->radius }}m
                    </span>
                @else
                    <span class="text-danger">School not configured!</span>
                @endif
                <br>
                
                <strong>Today's Date:</strong> {{ now()->toDateString() }}<br>
                
                <strong>Has Attended Today:</strong> {{ $hasAttendedToday ? 'Yes' : 'No' }}<br>
                
                <strong>Geolocation Test:</strong>
                <button onclick="testGeolocationSupport()" class="btn btn-sm btn-outline-dark">Test Support</button>
                <div id="geolocationTestResult" class="mt-1"></div>
            </small>
        </div>
    </details>
</div>

<script>
    function testGeolocationSupport() {
        const resultDiv = document.getElementById('geolocationTestResult');
        if (navigator.geolocation) {
            resultDiv.innerHTML = '<span class="text-success">✓ Geolocation supported</span>';
        } else {
            resultDiv.innerHTML = '<span class="text-danger">✗ Geolocation not supported</span>';
        }
    }
</script>
@endif
@endsection