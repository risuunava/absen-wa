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
                        <label for="photo_verified" class="form-label">Status Foto</label>
                        <select class="form-select" id="photo_verified" name="photo_verified" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            <option value="1" {{ request('photo_verified') == '1' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                            <option value="0" {{ request('photo_verified') == '0' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        </select>
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
                                    <th>Foto Selfie</th>
                                    <th>Validasi Foto</th>
                                    <th>Lokasi</th>
                                    <th>Jarak</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
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
                                            @if($attendance->selfie_photo)
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#photoModal{{ $attendance->id }}">
                                                    <i class="bi bi-eye"></i> Lihat Foto
                                                </button>
                                                
                                                <!-- Modal for Photo -->
                                                <div class="modal fade" id="photoModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Foto Selfie - {{ $attendance->user->full_name ?? $attendance->user->username }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset('storage/' . $attendance->selfie_photo) }}" 
                                                                     alt="Foto Selfie" class="img-fluid rounded" style="max-height: 500px;">
                                                                <div class="mt-3">
                                                                    <p class="text-muted">
                                                                        <small>
                                                                            Diambil pada: {{ $attendance->date->format('d/m/Y') }} {{ $attendance->time }}
                                                                        </small>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                @if(!$attendance->photo_verified)
                                                                    <form action="{{ route('admin.verify.photo', $attendance->id) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success">
                                                                            <i class="bi bi-check-circle"></i> Verifikasi Foto
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-warning">Tidak ada foto</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->selfie_photo)
                                                @if($attendance->photo_verified)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Sudah diverifikasi
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock-history"></i> Belum diverifikasi
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
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
                                        <td>
                                            @if($attendance->selfie_photo && !$attendance->photo_verified)
                                                <form action="{{ route('admin.verify.photo', $attendance->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Verifikasi Foto">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $attendance->id }}" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $attendance->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus absensi ini?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('admin.attendance.delete', $attendance->id) }}" method="POST">
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
                                    <h6>Total Absensi</h6>
                                    <h3>{{ count($attendances) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Foto Terverifikasi</h6>
                                    <h3>{{ $attendances->where('photo_verified', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Foto Belum Diverifikasi</h6>
                                    <h3>{{ $attendances->where('selfie_photo', '!=', null)->where('photo_verified', false)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Tanpa Foto</h6>
                                    <h3>{{ $attendances->where('selfie_photo', null)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Total absensi {{ $role }} pada {{ date('d/m/Y', strtotime($date)) }}: 
                            <strong>{{ count($attendances) }}</strong> data
                            | Foto terverifikasi: <strong>{{ $attendances->where('photo_verified', true)->count() }}</strong>
                            | Foto belum diverifikasi: <strong>{{ $attendances->where('selfie_photo', '!=', null)->where('photo_verified', false)->count() }}</strong>
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