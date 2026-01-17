@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Data Absensi</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form method="GET" action="{{ route('admin.attendance') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="{{ request('date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">Semua</option>
                                    <option value="murid" {{ request('role') == 'murid' ? 'selected' : '' }}>Murid</option>
                                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="photo_verified" class="form-label">Status Foto</label>
                                <select class="form-select" id="photo_verified" name="photo_verified">
                                    <option value="">Semua</option>
                                    <option value="1" {{ request('photo_verified') == '1' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                                    <option value="0" {{ request('photo_verified') == '0' ? 'selected' : '' }}>Belum Diverifikasi</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.attendance') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    @if(isset($attendances) && count($attendances) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Role</th>
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
                                    @foreach($attendances as $index => $attendance)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($attendance->user)
                                                    {{ $attendance->user->full_name ?? $attendance->user->username }}
                                                @else
                                                    <span class="text-danger">User tidak ditemukan</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $attendance->role }}</span>
                                            </td>
                                            <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                            <td>{{ $attendance->time }}</td>
                                            <td>
                                                @if($attendance->selfie_photo)
                                                    @php
                                                        $fileExists = file_exists(storage_path('app/public/' . $attendance->selfie_photo));
                                                    @endphp
                                                    @if($fileExists)
                                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#photoModal{{ $attendance->id }}">
                                                            <i class="bi bi-eye"></i> Lihat Foto
                                                        </button>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-exclamation-triangle"></i> File tidak ditemukan
                                                        </span>
                                                    @endif
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
                                                    <br>
                                                    <a href="https://maps.google.com/?q={{ $attendance->latitude }},{{ $attendance->longitude }}" 
                                                       target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                        <i class="bi bi-map"></i> Lihat di Map
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $attendance->distance <= ($school->radius ?? 100) ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $attendance->distance }} m
                                                </span>
                                            </td>
                                            <td>
                                                @if($attendance->status == 'hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($attendance->status == 'alpha')
                                                    <span class="badge bg-danger">Alpha</span>
                                                @elseif($attendance->status == 'izin')
                                                    <span class="badge bg-warning">Izin</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                                @endif
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
                                            </td>
                                        </tr>
                                        
                                        @if($attendance->selfie_photo && file_exists(storage_path('app/public/' . $attendance->selfie_photo)))
                                            <div class="modal fade" id="photoModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Selfie - {{ $attendance->user->full_name ?? $attendance->user->username ?? 'Unknown' }}</h5>
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
                                        @endif
                                        
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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
                                Total absensi {{ $role ?? 'Semua Role' }} pada {{ date('d/m/Y', strtotime($date ?? now())) }}: 
                                <strong>{{ count($attendances) }}</strong> data
                                | Foto terverifikasi: <strong>{{ $attendances->where('photo_verified', true)->count() }}</strong>
                                | Foto belum diverifikasi: <strong>{{ $attendances->where('selfie_photo', '!=', null)->where('photo_verified', false)->count() }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Tidak ada data absensi untuk tanggal yang dipilih.
                        </div>
                    @endif
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