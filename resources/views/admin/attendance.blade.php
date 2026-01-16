@extends('layouts.app')

@section('title', 'Data Absensi ' . ucfirst($role))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-calendar-check"></i> 
                Data Absensi {{ ucfirst($role) }}
            </h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" onchange="this.form.submit()">
                            <option value="murid" {{ $role === 'murid' ? 'selected' : '' }}>Murid</option>
                            <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="{{ route('admin.attendance') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Data -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Absensi {{ ucfirst($role) }} 
                    <span class="badge bg-light text-dark">{{ count($attendances) }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if(count($attendances) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    @if($role === 'murid')
                                        <th>Kelas</th>
                                    @else
                                        <th>Mata Pelajaran</th>
                                    @endif
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Lokasi</th>
                                    <th>Jarak</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $attendance->user->full_name ?? $attendance->user->username }}</td>
                                        <td>
                                            {{ $attendance->user->{$role === 'murid' ? 'class' : 'subject'} }}
                                        </td>
                                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->time }}</td>
                                        <td>
                                            @if($attendance->latitude && $attendance->longitude)
                                                <small>
                                                    {{ number_format($attendance->latitude, 6) }}, 
                                                    {{ number_format($attendance->longitude, 6) }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($attendance->distance, 0) }} m</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td>{{ $attendance->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Total absensi {{ $role }} pada {{ date('d/m/Y', strtotime($date)) }}: 
                            <strong>{{ count($attendances) }}</strong> data
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h4 class="mt-3">Tidak ada data absensi</h4>
                        <p class="text-muted">
                            Tidak ada data absensi {{ $role }} pada tanggal {{ date('d/m/Y', strtotime($date)) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection