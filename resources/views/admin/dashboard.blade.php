@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h1>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people"></i> Total Murid</h5>
                <h2 class="card-text display-6">{{ $totalMurid }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person-badge"></i> Total Guru</h5>
                <h2 class="card-text display-6">{{ $totalGuru }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-check"></i> Absensi Hari Ini</h5>
                <h2 class="card-text display-6">{{ $absensiHariIni }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-check-circle"></i> Valid Hari Ini</h5>
                <h2 class="card-text display-6">{{ $absensiValid }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- School Configuration -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Konfigurasi Lokasi Sekolah</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.school.update') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control" id="latitude" 
                                   name="latitude" value="{{ $school->latitude ?? '-6.2088' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control" id="longitude" 
                                   name="longitude" value="{{ $school->longitude ?? '106.8456' }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="radius" class="form-label">Radius Absensi (meter)</label>
                        <input type="number" class="form-control" id="radius" name="radius" 
                               value="{{ $school->radius ?? 100 }}" min="10" required>
                        <div class="form-text">Jarak maksimal dari sekolah untuk absensi valid</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.users') }}?role=murid" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-people"></i> Kelola Murid
                    </a>
                    <a href="{{ route('admin.users') }}?role=guru" class="btn btn-outline-success btn-lg">
                        <i class="bi bi-person-badge"></i> Kelola Guru
                    </a>
                    <a href="{{ route('admin.attendance') }}?role=murid&date={{ date('Y-m-d') }}" 
                       class="btn btn-outline-info btn-lg">
                        <i class="bi bi-calendar-check"></i> Lihat Absensi
                    </a>
                    <a href="{{ route('admin.time.settings') }}" class="btn btn-outline-warning btn-lg">
                        <i class="bi bi-clock"></i> Kelola Waktu Absen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Time Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Waktu Absen Aktif</h5>
            </div>
            <div class="card-body">
                @if($timeSettings->count() > 0)
                    <div class="row">
                        @foreach($timeSettings as $setting)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-{{ $setting->type == 'masuk' ? 'primary' : 'success' }}">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ $setting->name }}</h5>
                                        <h2 class="display-6">{{ $setting->start_time->format('H:i') }} - {{ $setting->end_time->format('H:i') }}</h2>
                                        <span class="badge bg-{{ $setting->type == 'masuk' ? 'primary' : 'success' }}">
                                            {{ ucfirst($setting->type) }}
                                        </span>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Durasi: {{ $setting->start_time->diffInHours($setting->end_time) }} jam
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="alert alert-light mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Total {{ $timeSettings->count() }} waktu absen aktif</strong> |
                        Masuk: {{ $timeSettings->where('type', 'masuk')->count() }} |
                        Pulang: {{ $timeSettings->where('type', 'pulang')->count() }}
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-clock display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada waktu absen aktif</h4>
                        <p class="text-muted">
                            <a href="{{ route('admin.time.settings') }}" class="btn btn-warning">
                                <i class="bi bi-plus-circle"></i> Tambah Waktu Absen
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendances -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Absensi Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Jarak</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->user->full_name ?? $attendance->user->username }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->role === 'murid' ? 'primary' : 'success' }}">
                                            {{ ucfirst($attendance->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                    <td>{{ $attendance->time }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->attendance_type === 'masuk' ? 'primary' : 'success' }}">
                                            {{ ucfirst($attendance->attendance_type) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($attendance->distance, 0) }} m</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data absensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('admin.attendance') }}" class="btn btn-outline-dark">
                        <i class="bi bi-list-ul"></i> Lihat Semua Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Time Info -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clock"></i> Waktu Server Saat Ini</h5>
            </div>
            <div class="card-body text-center">
                <h2 id="serverTime" class="display-4">{{ now()->format('H:i:s') }}</h2>
                <p class="text-muted mb-0">{{ now()->format('d F Y') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Status Absensi Hari Ini</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="row">
                        <div class="col-6">
                            <h3>{{ $absensiHariIni }}</h3>
                            <small class="text-muted">Total Absensi</small>
                        </div>
                        <div class="col-6">
                            <h3>{{ $absensiValid }}</h3>
                            <small class="text-muted">Absensi Valid</small>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted">Terakhir update: {{ now()->format('H:i:s') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.85em;
}
.status-valid {
    background-color: #d4edda;
    color: #155724;
}
.status-invalid {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
@endpush

@push('scripts')
<script>
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
    
    document.addEventListener('DOMContentLoaded', function() {
        updateServerTime();
        setInterval(updateServerTime, 1000);
    });
</script>
@endpush