@extends('layouts.app')

@section('title', 'Kelola ' . ucfirst($role))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-{{ $role === 'murid' ? 'people' : 'person-badge' }}"></i> 
                Kelola {{ ucfirst($role) }}
            </h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Create User Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Tambah {{ ucfirst($role) }} Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Nomor HP *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                    </div>
                    
                    @if($role === 'murid')
                        <div class="mb-3">
                            <label for="class" class="form-label">Kelas *</label>
                            <input type="text" class="form-control" id="class" name="class" 
                                   placeholder="Contoh: XII IPA 1" required>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="subject" class="form-label">Mata Pelajaran *</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   placeholder="Contoh: Matematika" required>
                        </div>
                    @endif
                    
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan {{ ucfirst($role) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Users List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list"></i> Daftar {{ ucfirst($role) }} 
                    <span class="badge bg-light text-dark">{{ count($users) }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Nomor HP</th>
                                @if($role === 'murid')
                                    <th>Kelas</th>
                                @else
                                    <th>Mata Pelajaran</th>
                                @endif
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->{$role === 'murid' ? 'class' : 'subject'} }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal{{ $user->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $user->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit {{ ucfirst($role) }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.users.edit', $user->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="username{{ $user->id }}" class="form-label">Username</label>
                                                                <input type="text" class="form-control" 
                                                                       id="username{{ $user->id }}" 
                                                                       name="username" value="{{ $user->username }}" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="phone{{ $user->id }}" class="form-label">Nomor HP</label>
                                                                <input type="tel" class="form-control" 
                                                                       id="phone{{ $user->id }}" 
                                                                       name="phone" value="{{ $user->phone }}" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="full_name{{ $user->id }}" class="form-label">Nama Lengkap</label>
                                                                <input type="text" class="form-control" 
                                                                       id="full_name{{ $user->id }}" 
                                                                       name="full_name" value="{{ $user->full_name }}" required>
                                                            </div>
                                                            
                                                            @if($role === 'murid')
                                                                <div class="mb-3">
                                                                    <label for="class{{ $user->id }}" class="form-label">Kelas</label>
                                                                    <input type="text" class="form-control" 
                                                                           id="class{{ $user->id }}" 
                                                                           name="class" value="{{ $user->class }}" required>
                                                                </div>
                                                            @else
                                                                <div class="mb-3">
                                                                    <label for="subject{{ $user->id }}" class="form-label">Mata Pelajaran</label>
                                                                    <input type="text" class="form-control" 
                                                                           id="subject{{ $user->id }}" 
                                                                           name="subject" value="{{ $user->subject }}" required>
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="mb-3">
                                                                <label for="password{{ $user->id }}" class="form-label">Password (biarkan kosong jika tidak diubah)</label>
                                                                <input type="password" class="form-control" 
                                                                       id="password{{ $user->id }}" 
                                                                       name="password" minlength="6">
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
                                        
                                        <!-- Delete Form -->
                                        <form id="deleteForm{{ $user->id }}" 
                                              action="{{ route('admin.users.delete', $user->id) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data {{ $role }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            document.getElementById('deleteForm' + userId).submit();
        }
    }
</script>
@endpush
@endsection