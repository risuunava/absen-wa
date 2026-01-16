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
                    Sistem absensi berbasis lokasi dan foto selfie untuk murid dan guru.
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
                        <!-- Attendance Button with Selfie -->
                        <div id="selfieSection" style="display: none;">
                            <h5 class="mb-3"><i class="bi bi-camera"></i> Ambil Foto Selfie</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mx-auto">
                                    <!-- Video Preview -->
                                    <video id="video" width="100%" height="auto" autoplay></video>
                                    <!-- Canvas for Capture -->
                                    <canvas id="canvas" width="400" height="300" style="display: none;"></canvas>
                                    
                                    <!-- Captured Image Preview -->
                                    <div id="capturePreview" class="mt-3" style="display: none;">
                                        <p class="text-muted">Preview Foto:</p>
                                        <img id="photoPreview" src="#" alt="Foto Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="captureBtn">
                                    <i class="bi bi-camera-fill"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-secondary" id="retakeBtn" style="display: none;">
                                    <i class="bi bi-arrow-clockwise"></i> Ambil Ulang
                                </button>
                            </div>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="bi bi-info-circle"></i> 
                                    Foto selfie akan digunakan untuk validasi kehadiran Anda.
                                    Pastikan wajah terlihat jelas dan pencahayaan cukup.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Attendance Form -->
                        <form id="attendanceForm" action="{{ route('hadir') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="attendanceLat">
                            <input type="hidden" name="longitude" id="attendanceLng">
                            <input type="hidden" name="distance" id="attendanceDistance">
                            <input type="hidden" name="selfie_photo" id="selfiePhotoInput">
                            
                            <button type="submit" class="btn btn-success attendance-btn mb-3" id="attendanceBtn" disabled>
                                <i class="bi bi-check-circle"></i> HADIR DENGAN FOTO
                            </button>
                            
                            <div id="attendanceMessage" class="text-muted">
                                Tombol akan aktif ketika Anda berada dalam radius sekolah
                            </div>
                        </form>
                    @endif
                    
                    @if($userRole === 'guru')
                        <div class="mt-3 text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Guru wajib melakukan absensi kehadiran dengan foto selfie seperti murid.
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
                        <strong>Fitur Utama:</strong> Absensi berbasis lokasi dan foto selfie dengan validasi realtime
                    </li>
                    <li class="list-group-item">
                        <strong>Teknologi:</strong> Geolocation API dan Webcam untuk deteksi posisi dan foto
                    </li>
                    <li class="list-group-item">
                        <strong>Validasi:</strong> Absensi hanya valid dalam radius sekolah dengan foto selfie
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
    let videoStream = null;
    let photoData = null;

    // Camera Functions
    function startCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: 400,
                    height: 300,
                    facingMode: 'user' // Front camera
                } 
            })
            .then(function(stream) {
                videoStream = stream;
                const video = document.getElementById('video');
                video.srcObject = stream;
                video.play();
                document.getElementById('selfieSection').style.display = 'block';
            })
            .catch(function(err) {
                console.error("Error accessing camera: ", err);
                // Fallback to file upload
                document.getElementById('selfieSection').innerHTML = `
                    <div class="alert alert-warning">
                        <p>Kamera tidak dapat diakses. Silakan upload foto:</p>
                        <input type="file" id="fileInput" accept="image/*" capture="user" class="form-control">
                    </div>
                `;
                document.getElementById('fileInput').addEventListener('change', handleFileUpload);
            });
        } else {
            console.error("getUserMedia not supported");
            document.getElementById('selfieSection').innerHTML = `
                <div class="alert alert-warning">
                    <p>Browser tidak mendukung akses kamera. Silakan gunakan browser lain.</p>
                </div>
            `;
        }
    }

    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        
        if (!video || !canvas) {
            alert('Elemen video atau canvas tidak ditemukan');
            return;
        }
        
        const context = canvas.getContext('2d');
        
        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert canvas to data URL
        photoData = canvas.toDataURL('image/jpeg', 0.8); // Quality 80%
        
        // Show preview
        document.getElementById('photoPreview').src = photoData;
        document.getElementById('capturePreview').style.display = 'block';
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'inline-block';
        
        // Set photo data to hidden input
        document.getElementById('selfiePhotoInput').value = photoData;
        
        // Update button state
        updateAttendanceButton();
    }

    function retakePhoto() {
        document.getElementById('capturePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('retakeBtn').style.display = 'none';
        document.getElementById('selfiePhotoInput').value = '';
        photoData = null;
        
        // Update button state
        updateAttendanceButton();
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoData = e.target.result;
                document.getElementById('selfiePhotoInput').value = photoData;
                
                // Show preview
                document.getElementById('photoPreview').src = photoData;
                document.getElementById('capturePreview').style.display = 'block';
                
                // Update button state
                updateAttendanceButton();
            };
            reader.readAsDataURL(file);
        }
    }

    function updateAttendanceButton() {
        const attendanceBtn = document.getElementById('attendanceBtn');
        const attendanceMessage = document.getElementById('attendanceMessage');
        
        if (attendanceBtn) {
            if (isWithinRadius && photoData) {
                attendanceBtn.disabled = false;
                attendanceBtn.classList.remove('btn-secondary');
                attendanceBtn.classList.add('btn-success');
                attendanceMessage.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Anda dapat melakukan absensi</span>';
            } else if (isWithinRadius && !photoData) {
                attendanceBtn.disabled = true;
                attendanceBtn.classList.remove('btn-success');
                attendanceBtn.classList.add('btn-secondary');
                attendanceMessage.innerHTML = '<span class="text-warning"><i class="bi bi-camera"></i> Ambil foto selfie terlebih dahulu</span>';
            } else {
                attendanceBtn.disabled = true;
                attendanceBtn.classList.remove('btn-success');
                attendanceBtn.classList.add('btn-secondary');
                attendanceMessage.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Anda harus berada dalam radius sekolah untuk absensi</span>';
            }
        }
    }

    function updateDistanceInfo(distance, isWithin) {
        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        const locationStatus = document.getElementById('locationStatus');
        
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
        } else {
            statusElement.innerHTML = '<span class="badge bg-danger">Di Luar Area Sekolah</span>';
            locationStatus.innerHTML = '<i class="bi bi-exclamation-circle"></i> Anda berada di luar radius absensi';
        }
        
        // Update button state
        updateAttendanceButton();
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
        e.preventDefault(); // Prevent default untuk debugging
        
        console.log('Form submission started');
        console.log('Is within radius:', isWithinRadius);
        console.log('Current distance:', currentDistance);
        console.log('Photo data:', photoData ? 'Yes' : 'No');
        
        if (!isWithinRadius) {
            alert('Anda harus berada dalam radius sekolah untuk melakukan absensi.');
            return false;
        }
        
        if (!currentDistance) {
            alert('Sedang mengambil lokasi. Silakan tunggu sebentar.');
            return false;
        }
        
        if (!photoData) {
            alert('Silakan ambil foto selfie terlebih dahulu.');
            return false;
        }
        
        // Show confirmation
        if (confirm(`Anda berada ${Math.round(currentDistance)} meter dari sekolah. Foto selfie akan digunakan untuk validasi. Lanjutkan absensi?`)) {
            console.log('Submitting form...');
            
            // Show loading state
            const submitBtn = document.getElementById('attendanceBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            this.submit();
        }
        
        return false;
    });

    // Button untuk manual retry
    function retryLocation() {
        console.log('Manual retry location');
        getLocation();
    }

    // Initialize on page load
    $(document).ready(function() {
        console.log('Document ready, initializing geolocation...');
        
        // Start camera if user is logged in and can attend
        @if(session()->has('user_id') && !$hasAttendedToday)
            startCamera();
        @endif
        
        // Event listeners for camera buttons
        document.getElementById('captureBtn')?.addEventListener('click', capturePhoto);
        document.getElementById('retakeBtn')?.addEventListener('click', retakePhoto);
        
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