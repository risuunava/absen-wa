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
                    Sistem absensi berbasis lokasi, foto selfie, dan waktu untuk murid dan guru.
                    Pastikan Anda berada dalam radius sekolah dan pada waktu yang ditentukan untuk melakukan absensi.
                </p>
                
                @if($school)
                    <div class="alert alert-info">
                        <i class="bi bi-geo-alt"></i> 
                        Lokasi Sekolah: {{ $school->name }}<br>
                        Radius Absensi: {{ $school->radius }} meter
                    </div>
                @endif

                <!-- TIMEZONE INFO (Hanya tampil di development) -->
                @if(env('APP_DEBUG', false))
                <div class="alert alert-dark">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        <strong>Timezone Info:</strong> 
                        <span id="timezoneInfo">Loading...</span>
                    </small>
                </div>
                @endif

                <!-- Attendance Time Information -->
                <div class="alert" id="timeInfoAlert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock me-2 fs-4"></i>
                        <div>
                            <strong id="timeInfoTitle">Memuat informasi waktu absen...</strong>
                            <div id="timeInfoContent">
                                <div class="spinner-border spinner-border-sm text-dark" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <small>User ID: {{ session('user_id') }}</small>
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
                                <div class="spinner-border spinner-border-sm me-2" role="status" id="timeCheckSpinner">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span id="attendanceMessageText">
                                    Memeriksa kondisi absensi...
                                </span>
                            </div>
                        </form>
                        
                        <!-- Debug Buttons (Only in debug mode) -->
                        @if(env('APP_DEBUG', false))
                        <div class="mt-3">
                            <button onclick="testTimeCheck()" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-clock"></i> Test Waktu
                            </button>
                            <button onclick="forceEnableAttendance()" class="btn btn-sm btn-outline-warning ms-2">
                                <i class="bi bi-unlock"></i> Force Enable
                            </button>
                        </div>
                        @endif
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
                        <strong>Fitur Utama:</strong> Absensi berbasis lokasi, waktu, dan foto selfie dengan validasi realtime
                    </li>
                    <li class="list-group-item">
                        <strong>Validasi Waktu:</strong> Absensi hanya dapat dilakukan pada jam yang telah ditentukan admin
                    </li>
                    <li class="list-group-item">
                        <strong>Validasi Lokasi:</strong> Absensi hanya valid dalam radius sekolah
                    </li>
                    <li class="list-group-item">
                        <strong>Validasi Foto:</strong> Wajib mengambil foto selfie untuk setiap absensi
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
    let isAllowedTime = false;
    let timeMessage = 'Memeriksa waktu...';
    let activeTimeSettings = [];
    let serverTimezone = 'Loading...';

    // ============================================
    // TIMEZONE FUNCTIONS
    // ============================================

    // Fungsi untuk menampilkan info timezone
    function updateTimezoneInfo() {
        const now = new Date();
        const clientTime = now.toLocaleTimeString('id-ID');
        const clientDate = now.toLocaleDateString('id-ID');
        const clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        document.getElementById('timezoneInfo').innerHTML = `
            Client: ${clientDate} ${clientTime} (${clientTimezone}) |
            Server: <span id="serverTime">${serverTimezone}</span>
        `;
    }

    // ============================================
    // CAMERA FUNCTIONS
    // ============================================

    function startCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: 400,
                    height: 300,
                    facingMode: 'user'
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
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        photoData = canvas.toDataURL('image/jpeg', 0.8);
        
        document.getElementById('photoPreview').src = photoData;
        document.getElementById('capturePreview').style.display = 'block';
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'inline-block';
        
        document.getElementById('selfiePhotoInput').value = photoData;
        
        updateAttendanceButton();
    }

    function retakePhoto() {
        document.getElementById('capturePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('retakeBtn').style.display = 'none';
        document.getElementById('selfiePhotoInput').value = '';
        photoData = null;
        
        updateAttendanceButton();
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoData = e.target.result;
                document.getElementById('selfiePhotoInput').value = photoData;
                
                document.getElementById('photoPreview').src = photoData;
                document.getElementById('capturePreview').style.display = 'block';
                
                updateAttendanceButton();
            };
            reader.readAsDataURL(file);
        }
    }

    // ============================================
    // TIME CHECK FUNCTIONS
    // ============================================

    // Load time settings - FUNCTION UTAMA UNTUK CHECK WAKTU
    function loadTimeSettings() {
        console.log('Memuat pengaturan waktu dari server...');
        
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
                
                // Update server timezone info
                if (response.timezone) {
                    serverTimezone = response.timezone;
                }
                if (response.server_time) {
                    serverTimezone = response.server_time + ' (' + (response.timezone || 'Asia/Jakarta') + ')';
                }
                
                updateTimeInfoDisplay(response);
                updateAttendanceButton();
                
                // Hide spinner setelah data diterima
                document.getElementById('timeCheckSpinner').style.display = 'none';
            },
            error: function(xhr, status, error) {
                console.error('❌ Gagal memuat pengaturan waktu:', error);
                console.error('Response:', xhr.responseText);
                
                // Fallback untuk testing
                const now = new Date();
                const clientTime = now.toLocaleTimeString('id-ID', {hour12: false});
                const clientDay = now.toLocaleDateString('id-ID', {weekday: 'long'});
                const hour = now.getHours().toString().padStart(2, '0');
                const minute = now.getMinutes().toString().padStart(2, '0');
                
                // Untuk testing, anggap waktu diizinkan jika antara 07:00 - 16:00
                const isTestingAllowed = (hour >= 7 && hour < 16);
                
                isAllowedTime = isTestingAllowed;
                timeMessage = isTestingAllowed ? 
                    'Mode testing - waktu diizinkan (07:00-16:00)' : 
                    'Mode testing - waktu tidak diizinkan (07:00-16:00)';
                activeTimeSettings = [];
                
                updateTimeInfoDisplay({
                    allowed: isTestingAllowed,
                    message: timeMessage,
                    current_time: clientTime,
                    current_hour_minute: hour + ':' + minute,
                    current_day_name: clientDay
                });
                updateAttendanceButton();
                
                // Hide spinner
                document.getElementById('timeCheckSpinner').style.display = 'none';
            }
        });
    }

    function updateTimeInfoDisplay(response) {
        const timeAlert = document.getElementById('timeInfoAlert');
        const timeTitle = document.getElementById('timeInfoTitle');
        const timeContent = document.getElementById('timeInfoContent');
        
        if (isAllowedTime) {
            timeAlert.className = 'alert alert-success';
            timeTitle.innerHTML = '<i class="bi bi-check-circle"></i> Waktu Absen DIJINKAN';
            
            let settingsHtml = '';
            if (activeTimeSettings && activeTimeSettings.length > 0) {
                settingsHtml = '<div class="mt-2"><small>Waktu yang diizinkan:</small><div>';
                activeTimeSettings.forEach(setting => {
                    settingsHtml += `<span class="badge bg-light text-dark me-1 mb-1">
                        ${setting.name}: ${setting.time_range} (${setting.type})
                    </span>`;
                });
                settingsHtml += '</div></div>';
            }
            
            timeContent.innerHTML = `
                <div><strong>${timeMessage}</strong></div>
                <small>Waktu server: ${response.current_hour_minute || response.current_time || ''}</small>
                <br>
                <small>Hari: ${response.current_day_name || ''}</small>
                ${settingsHtml}
            `;
        } else {
            timeAlert.className = 'alert alert-danger';
            timeTitle.innerHTML = '<i class="bi bi-x-circle"></i> Waktu Absen TIDAK DIJINKAN';
            
            let settingsHtml = '';
            if (activeTimeSettings && activeTimeSettings.length > 0) {
                settingsHtml = '<div class="mt-2"><small><strong>Waktu absen yang diizinkan:</strong></small><ul class="mb-0">';
                activeTimeSettings.forEach(setting => {
                    settingsHtml += `<li><strong>${setting.name}</strong>: ${setting.time_range} (${setting.type})</li>`;
                });
                settingsHtml += '</ul></div>';
            }
            
            timeContent.innerHTML = `
                <div><strong>${timeMessage}</strong></div>
                <small>Waktu server: ${response.current_hour_minute || response.current_time || ''}</small>
                <br>
                <small>Hari: ${response.current_day_name || ''}</small>
                ${settingsHtml}
            `;
        }
    }

    function testTimeCheck() {
        console.log('Manual time check triggered...');
        document.getElementById('timeCheckSpinner').style.display = 'inline-block';
        loadTimeSettings();
    }

    // ============================================
    // ATTENDANCE BUTTON FUNCTIONS
    // ============================================

    function updateAttendanceButton() {
        const attendanceBtn = document.getElementById('attendanceBtn');
        const attendanceMessage = document.getElementById('attendanceMessageText');
        const timeSpinner = document.getElementById('timeCheckSpinner');
        
        if (attendanceBtn) {
            if (isWithinRadius && photoData && isAllowedTime) {
                attendanceBtn.disabled = false;
                attendanceBtn.className = 'btn btn-success attendance-btn mb-3';
                attendanceMessage.innerHTML = `
                    <i class="bi bi-check-circle"></i> 
                    Anda dapat melakukan absensi
                    <br>
                    <small>${timeMessage}</small>`;
                timeSpinner.style.display = 'none';
            } else if (!isAllowedTime) {
                attendanceBtn.disabled = true;
                attendanceBtn.className = 'btn btn-secondary attendance-btn mb-3';
                attendanceMessage.innerHTML = `
                    <i class="bi bi-clock"></i> 
                    ${timeMessage}
                    <br>
                    <small>Absensi hanya dapat dilakukan pada jam yang ditentukan</small>`;
                timeSpinner.style.display = 'none';
            } else if (isWithinRadius && !photoData) {
                attendanceBtn.disabled = true;
                attendanceBtn.className = 'btn btn-warning attendance-btn mb-3';
                attendanceMessage.innerHTML = `
                    <i class="bi bi-camera"></i> 
                    Ambil foto selfie terlebih dahulu
                    <br>
                    <small>${timeMessage}</small>`;
                timeSpinner.style.display = 'none';
            } else if (!isWithinRadius) {
                attendanceBtn.disabled = true;
                attendanceBtn.className = 'btn btn-secondary attendance-btn mb-3';
                attendanceMessage.innerHTML = `
                    <i class="bi bi-geo-alt"></i> 
                    Anda harus berada dalam radius sekolah untuk absensi
                    <br>
                    <small>${timeMessage}</small>`;
                timeSpinner.style.display = 'none';
            } else {
                attendanceBtn.disabled = true;
                attendanceBtn.className = 'btn btn-secondary attendance-btn mb-3';
                attendanceMessage.innerHTML = `
                    <i class="bi bi-hourglass"></i> 
                    Memeriksa kondisi absensi...
                    <br>
                    <small>${timeMessage}</small>`;
            }
        }
    }

    // Force enable untuk testing (debug mode)
    function forceEnableAttendance() {
        if (!confirm('Force enable tombol absensi? Hanya untuk testing!')) return;
        
        isAllowedTime = true;
        isWithinRadius = true;
        photoData = 'data:image/jpeg;base64,test';
        document.getElementById('selfiePhotoInput').value = photoData;
        
        updateAttendanceButton();
        
        alert('Tombol absensi di-force enable untuk testing!');
    }

    // ============================================
    // LOCATION FUNCTIONS
    // ============================================

    function updateDistanceInfo(distance, isWithin) {
        const distanceElement = document.getElementById('distanceValue');
        const statusElement = document.getElementById('attendanceStatus');
        const locationStatus = document.getElementById('locationStatus');
        
        let displayDistance = 'Error';
        if (distance !== null && !isNaN(distance)) {
            displayDistance = Math.round(distance) + ' meter';
        }
        
        distanceElement.textContent = displayDistance;
        currentDistance = distance;
        isWithinRadius = isWithin;
        
        document.getElementById('attendanceDistance').value = distance || 0;
        
        if (isWithin) {
            statusElement.innerHTML = '<span class="badge bg-success">Dalam Area Sekolah</span>';
            locationStatus.innerHTML = '<i class="bi bi-check-circle"></i> Anda berada dalam radius absensi';
        } else {
            statusElement.innerHTML = '<span class="badge bg-danger">Di Luar Area Sekolah</span>';
            locationStatus.innerHTML = '<i class="bi bi-exclamation-circle"></i> Anda berada di luar radius absensi';
        }
        
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
            attendanceBtn.className = 'btn btn-secondary attendance-btn mb-3';
        }
    }

    function showSuccess(message) {
        document.getElementById('locationStatus').innerHTML = 
            `<i class="bi bi-check-circle"></i> ${message}`;
    }

    function getLocation() {
        if (!navigator.geolocation) {
            showError('Browser tidak mendukung Geolocation API');
            return;
        }

        document.getElementById('distanceValue').textContent = 'Mengambil lokasi...';
        document.getElementById('attendanceStatus').innerHTML = 
            '<span class="badge bg-info">Meminta izin lokasi...</span>';
        
        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
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
                        showSuccess('Lokasi berhasil diperbarui');
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
                
                // Default location untuk testing
                const schoolLat = {{ $school->latitude ?? -6.8632300 }};
                const schoolLng = {{ $school->longitude ?? 108.0491849 }};
                const distance = 50; // Default 50 meter
                const radius = {{ $school->radius ?? 100 }};
                
                document.getElementById('attendanceLat').value = schoolLat;
                document.getElementById('attendanceLng').value = schoolLng;
                updateDistanceInfo(distance, distance <= radius);
            },
            options
        );

        // Setup watch position untuk update realtime
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

    function retryLocation() {
        getLocation();
    }

    // ============================================
    // FORM SUBMISSION
    // ============================================

    document.getElementById('attendanceForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!isWithinRadius) {
            alert('Anda harus berada dalam radius sekolah untuk melakukan absensi.');
            return false;
        }
        
        if (!isAllowedTime) {
            alert('Anda hanya dapat melakukan absensi pada jam yang telah ditentukan.');
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
        
        if (confirm(`Anda berada ${Math.round(currentDistance)} meter dari sekolah. Lanjutkan absensi?`)) {
            const submitBtn = document.getElementById('attendanceBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            this.submit();
        }
        
        return false;
    });

    // ============================================
    // INITIALIZATION
    // ============================================

    $(document).ready(function() {
        console.log('Landing page initialized');
        
        // Update timezone info setiap detik
        updateTimezoneInfo();
        setInterval(updateTimezoneInfo, 1000);
        
        // Start camera jika user login dan belum absen
        @if(session()->has('user_id') && !$hasAttendedToday)
            console.log('Starting camera for attendance...');
            startCamera();
        @endif
        
        // Setup camera buttons
        document.getElementById('captureBtn')?.addEventListener('click', capturePhoto);
        document.getElementById('retakeBtn')?.addEventListener('click', retakePhoto);
        
        // Load time settings pertama kali
        console.log('Loading initial time settings...');
        loadTimeSettings();
        
        // Refresh time settings setiap 30 detik
        setInterval(loadTimeSettings, 30000);
        
        // Get location setelah 1 detik
        setTimeout(function() {
            console.log('Getting location...');
            getLocation();
        }, 1000);
        
        // Add control buttons
        $('#distanceInfo').append(`
            <div class="mt-3">
                <button onclick="retryLocation()" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Lokasi
                </button>
                <button onclick="loadTimeSettings()" class="btn btn-sm btn-outline-primary ms-2">
                    <i class="bi bi-clock"></i> Refresh Waktu
                </button>
            </div>
        `);
    });
</script>
@endpush
@endsection