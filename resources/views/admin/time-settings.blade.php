@extends('layouts.app')

@section('title', 'Pengaturan Waktu Absen')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-clock"></i> 
                Pengaturan Waktu Absen
            </h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Current Time Info -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="mb-2"><i class="bi bi-clock"></i> Waktu Server</h5>
                <h2 id="serverTime" class="display-4">{{ now()->format('H:i:s') }}</h2>
                <p class="mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="mb-2"><i class="bi bi-calendar-check"></i> Status Absensi</h5>
                <div id="attendanceStatusInfo">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Memeriksa waktu absen...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Time Setting Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Waktu Absen Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.time.settings.create') }}" method="POST" id="timeSettingForm">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Nama Waktu Absen</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Contoh: Absen Masuk Pagi" required>
                            <small class="text-muted">Nama yang mudah dipahami</small>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="start_time" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" 
                                   value="07:00" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="end_time" class="form-label">Jam Berakhir</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" 
                                   value="08:00" required>
                            <small class="text-muted">Set 00:00 untuk waktu tengah malam</small>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="type" class="form-label">Tipe Absen</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="masuk">Masuk</option>
                                <option value="pulang">Pulang</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Hari Berlaku</label>
                            <div class="row">
                                @php
                                    $days = [
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                        7 => 'Minggu'
                                    ];
                                @endphp
                                
                                @foreach($days as $key => $day)
                                    <div class="col-md-2 col-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="days_of_week[]" 
                                                   value="{{ $key }}" 
                                                   id="day{{ $key }}"
                                                   {{ in_array($key, [1,2,3,4,5]) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day{{ $key }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div class="col-12 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="select_all_days">
                                        <label class="form-check-label text-muted" for="select_all_days">
                                            Pilih Semua Hari
                                        </label>
                                    </div>
                                    <small class="text-muted">Kosongkan jika berlaku setiap hari</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="description" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="2" placeholder="Contoh: Waktu absen khusus untuk ujian"></textarea>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0">
                                    <li>Waktu absen bisa untuk jam berapa saja (tidak hanya jam 7)</li>
                                    <li>Admin bisa setting multiple time slots</li>
                                    <li>Bisa atur hari tertentu saja (misal: Senin-Jumat)</li>
                                    <li>Bisa dinonaktifkan sementara tanpa menghapus</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Simpan Waktu Absen
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Time Settings List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Daftar Waktu Absen
                    <span class="badge bg-light text-dark">{{ $settings->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($settings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Waktu</th>
                                    <th>Tipe</th>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th>Digunakan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                    @php
                                        // Parse days_of_week untuk edit form
                                        $daysData = $setting->days_of_week;
                                        $currentDaysArray = [];
                                        
                                        if (!empty($daysData)) {
                                            if (is_array($daysData)) {
                                                $currentDaysArray = $daysData;
                                            } elseif (is_string($daysData)) {
                                                $decoded = json_decode($daysData, true);
                                                $currentDaysArray = is_array($decoded) ? $decoded : [];
                                            }
                                        }
                                        
                                        // Untuk display
                                        $daysDisplay = 'Setiap Hari';
                                        if (!empty($currentDaysArray)) {
                                            $dayNames = [
                                                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
                                                4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
                                            ];
                                            $displayDays = array_map(function($day) use ($dayNames) {
                                                return $dayNames[$day] ?? $day;
                                            }, $currentDaysArray);
                                            $daysDisplay = implode(', ', $displayDays);
                                        }
                                        
                                        // Cek apakah aktif hari ini
                                        $isActiveToday = false;
                                        if ($setting->is_active && !empty($currentDaysArray)) {
                                            $currentDay = now()->dayOfWeekIso;
                                            $isActiveToday = in_array($currentDay, $currentDaysArray);
                                        } elseif ($setting->is_active) {
                                            $isActiveToday = true;
                                        }
                                        
                                        // Hitung penggunaan
                                        $usageCount = \App\Models\Attendance::where('attendance_setting_id', $setting->id)->count();
                                    @endphp
                                    
                                    <tr class="{{ $setting->is_active ? '' : 'table-secondary' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $setting->name }}</strong>
                                            @if($setting->description)
                                                <br>
                                                <small class="text-muted">{{ $setting->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $setting->start_time->format('H:i') }} - {{ $setting->end_time->format('H:i') }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Durasi: {{ $setting->start_time->diff($setting->end_time)->format('%H jam %I menit') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $setting->type == 'masuk' ? 'primary' : ($setting->type == 'pulang' ? 'success' : 'warning') }}">
                                                {{ ucfirst($setting->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $daysDisplay }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $isActiveToday ? '✓ Berlaku hari ini' : '✗ Tidak berlaku hari ini' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($setting->is_active)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $usageCount }} kali</span>
                                        </td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal{{ $setting->id }}"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            @if($usageCount == 0)
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $setting->id }}"
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-muted" title="Tidak dapat dihapus karena sudah digunakan">
                                                    <i class="bi bi-lock"></i>
                                                </span>
                                            @endif
                                            
                                            <!-- EDIT FORM (di dalam modal) -->
<div class="modal fade" id="editModal{{ $setting->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Waktu Absen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.time.settings.update', $setting->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name{{ $setting->id }}" class="form-label">Nama Waktu Absen</label>
                        <input type="text" class="form-control" id="edit_name{{ $setting->id }}" 
                               name="name" value="{{ $setting->name }}" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_start_time{{ $setting->id }}" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="edit_start_time{{ $setting->id }}" 
                                   name="start_time" value="{{ $setting->start_time->format('H:i') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_end_time{{ $setting->id }}" class="form-label">Jam Berakhir</label>
                            <input type="time" class="form-control" id="edit_end_time{{ $setting->id }}" 
                                   name="end_time" value="{{ $setting->end_time->format('H:i') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_type{{ $setting->id }}" class="form-label">Tipe Absen</label>
                        <select class="form-select" id="edit_type{{ $setting->id }}" name="type" required>
                            <option value="masuk" {{ $setting->type == 'masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="pulang" {{ $setting->type == 'pulang' ? 'selected' : '' }}>Pulang</option>
                            <option value="lainnya" {{ $setting->type == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hari Berlaku</label>
                        <div class="row">
                            @php
                                $days = [
                                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
                                    4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
                                ];
                                
                                // Parse days_of_week untuk edit form
                                $daysData = $setting->days_of_week;
                                $currentDaysArray = [];
                                
                                if (!empty($daysData)) {
                                    if (is_array($daysData)) {
                                        $currentDaysArray = $daysData;
                                    } elseif (is_string($daysData)) {
                                        $decoded = json_decode($daysData, true);
                                        $currentDaysArray = is_array($decoded) ? $decoded : [];
                                    }
                                }
                            @endphp
                            
                            @foreach($days as $key => $day)
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="days_of_week[]" 
                                               value="{{ $key }}" 
                                               id="edit_day{{ $key }}_{{ $setting->id }}"
                                               {{ in_array($key, $currentDaysArray) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_day{{ $key }}_{{ $setting->id }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description{{ $setting->id }}" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="edit_description{{ $setting->id }}" 
                                  name="description" rows="2">{{ $setting->description }}</textarea>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" 
                               id="edit_is_active{{ $setting->id }}" 
                               name="is_active" value="1" 
                               {{ $setting->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_is_active{{ $setting->id }}">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE FORM (di dalam modal) -->
<div class="modal fade" id="deleteModal{{ $setting->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengaturan waktu absen <strong>"{{ $setting->name }}"</strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.time.settings.delete', $setting->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Total Setting</h6>
                                    <h3>{{ $settings->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Aktif</h6>
                                    <h3>{{ $settings->where('is_active', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Tipe Masuk</h6>
                                    <h3>{{ $settings->where('type', 'masuk')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Tipe Pulang</h6>
                                    <h3>{{ $settings->where('type', 'pulang')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Panel -->
                    <div class="alert alert-light mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi Sistem:</strong>
                        <ul class="mb-0">
                            <li>Total {{ $settings->count() }} pengaturan waktu absen</li>
                            <li>{{ $settings->where('is_active', true)->count() }} aktif, {{ $settings->where('is_active', false)->count() }} nonaktif</li>
                            <li>Sistem akan memvalidasi waktu absen berdasarkan setting aktif</li>
                            <li>User hanya bisa absen pada waktu yang sudah ditentukan</li>
                        </ul>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clock display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada pengaturan waktu</h4>
                        <p class="text-muted">
                            Tambahkan pengaturan waktu absen menggunakan form di atas.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.badge {
    font-size: 0.85em;
}
.table-secondary {
    opacity: 0.7;
}
</style>
@endpush

@push('scripts')
<script>
    // Update server time every second
    function updateServerTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        const dateString = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        document.getElementById('serverTime').textContent = timeString;
    }
    
    // Check attendance status
    function checkAttendanceStatus() {
        $.ajax({
            url: '{{ route("check.attendance.time") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const statusDiv = document.getElementById('attendanceStatusInfo');
                
                if (response.allowed) {
                    statusDiv.innerHTML = `
                        <h4 class="text-success mb-2">
                            <i class="bi bi-check-circle"></i> ABSENSI DIJINKAN
                        </h4>
                        <p class="mb-1">${response.message}</p>
                        <small>Waktu: ${response.current_time}</small>
                    `;
                } else {
                    statusDiv.innerHTML = `
                        <h4 class="text-danger mb-2">
                            <i class="bi bi-x-circle"></i> ABSENSI DITOLAK
                        </h4>
                        <p class="mb-1">${response.message}</p>
                        <small>Waktu: ${response.current_time}</small>
                    `;
                }
            },
            error: function() {
                document.getElementById('attendanceStatusInfo').innerHTML = 
                    '<span class="text-warning">Gagal memeriksa status</span>';
            }
        });
    }
    
    // Select all days checkbox
    document.getElementById('select_all_days')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="days_of_week[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Reset form
    function resetForm() {
        document.getElementById('timeSettingForm').reset();
        const checkboxes = document.querySelectorAll('input[name="days_of_week[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    
    // Validate time range
    document.getElementById('timeSettingForm')?.addEventListener('submit', function(e) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime && endTime && endTime !== '00:00') {
            if (startTime >= endTime) {
                e.preventDefault();
                alert('Waktu mulai harus lebih awal dari waktu berakhir!');
                return false;
            }
        }
        return true;
    });
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateServerTime();
        setInterval(updateServerTime, 1000);
        
        checkAttendanceStatus();
        setInterval(checkAttendanceStatus, 30000); // Check every 30 seconds
        
        // Auto-update attendance status every 10 seconds
        setInterval(checkAttendanceStatus, 10000);
    });
</script>
@endpush