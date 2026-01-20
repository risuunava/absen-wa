@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6 md:py-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Data Absensi</h1>
                <p class="text-sm text-gray-600">Kelola dan monitor seluruh data kehadiran</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="glass-card rounded-xl px-4 py-3 min-w-[120px]">
                    <div class="text-xs text-gray-500 mb-1">Total Data</div>
                    <div class="text-lg font-bold text-gray-900">{{ $attendances->count() ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 animate-fade-in">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-green-600 mobile-icon-md"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                            class="ml-4 text-green-600 hover:text-green-800">
                        <i class="bi bi-x mobile-icon-md"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 animate-fade-in">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-circle text-red-600 mobile-icon-md"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                            class="ml-4 text-red-600 hover:text-red-800">
                        <i class="bi bi-x mobile-icon-md"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-card rounded-xl p-5 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-calendar-check text-blue-600 mobile-icon-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ count($attendances) }}</div>
                    <div class="text-sm text-gray-600">Total Absensi</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-check-circle text-green-600 mobile-icon-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $attendances->where('photo_verified', true)->count() }}</div>
                    <div class="text-sm text-gray-600">Foto Terverifikasi</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-clock-history text-yellow-600 mobile-icon-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $attendances->where('selfie_photo', '!=', null)->where('photo_verified', false)->count() }}</div>
                    <div class="text-sm text-gray-600">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-x-circle text-gray-600 mobile-icon-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $attendances->where('selfie_photo', null)->count() }}</div>
                    <div class="text-sm text-gray-600">Tanpa Foto</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card rounded-xl p-5 mb-6 animate-fade-in">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-funnel text-gray-600 mobile-icon-md"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Filter Data</h2>
                <p class="text-sm text-gray-600">Saring data absensi sesuai kebutuhan</p>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.attendance') }}" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" id="date" name="date" 
                               value="{{ request('date', date('Y-m-d')) }}"
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm">
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-people text-gray-400"></i>
                        </div>
                        <select id="role" name="role" 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm appearance-none">
                            <option value="">Semua Role</option>
                            <option value="murid" {{ request('role') == 'murid' ? 'selected' : '' }}>Murid</option>
                            <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Photo Verification Filter -->
                <div>
                    <label for="photo_verified" class="block text-sm font-medium text-gray-700 mb-2">Status Foto</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-camera text-gray-400"></i>
                        </div>
                        <select id="photo_verified" name="photo_verified" 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm appearance-none">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('photo_verified') == '1' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                            <option value="0" {{ request('photo_verified') == '0' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Kehadiran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-check-circle text-gray-400"></i>
                        </div>
                        <select id="status" name="status" 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm appearance-none">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium">
                    <i class="bi bi-search mobile-icon-sm"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('admin.attendance') }}" class="btn-secondary flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium">
                    <i class="bi bi-arrow-clockwise mobile-icon-sm"></i>
                    <span>Reset Filter</span>
                </a>
            </div>
        </form>
    </div>

    @if(isset($attendances) && count($attendances) > 0)
        <!-- Data Table Section -->
        <div class="glass-card rounded-xl overflow-hidden animate-fade-in">
            <!-- Table Header -->
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Data Absensi</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $role ? ucfirst($role) : 'Semua Role' }} • {{ date('d F Y', strtotime($date ?? now())) }}
                        </p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-bold text-gray-900">{{ $attendances->count() }}</span> data
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Role</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal/Waktu</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status Foto</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jarak</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attendances as $index => $attendance)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium text-center">{{ $index + 1 }}</td>
                            
                            <td class="py-4 px-4">
                                @if($attendance->user)
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $attendance->user->full_name ?? $attendance->user->username }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">ID: {{ $attendance->user_id }}</div>
                                    </div>
                                @else
                                    <span class="text-red-600 text-sm">User tidak ditemukan</span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $attendance->role == 'murid' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                                       ($attendance->role == 'guru' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                       'bg-gray-100 text-gray-800 border border-gray-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        {{ $attendance->role == 'murid' ? 'bg-blue-500' : 
                                           ($attendance->role == 'guru' ? 'bg-green-500' : 
                                           'bg-gray-500') }}"></span>
                                    {{ ucfirst($attendance->role) }}
                                </span>
                            </td>
                            
                            <td class="py-4 px-4">
                                <div class="text-sm font-medium text-gray-900">{{ $attendance->date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $attendance->time }}</div>
                            </td>
                            
                            <!-- Status Foto Column -->
                            <td class="py-4 px-4">
                                @if($attendance->selfie_photo)
                                    @php
                                        $fileExists = file_exists(storage_path('app/public/' . $attendance->selfie_photo));
                                    @endphp
                                    @if($fileExists)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $attendance->photo_verified ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                                            <i class="bi {{ $attendance->photo_verified ? 'bi-check-circle' : 'bi-clock' }} mr-1"></i>
                                            {{ $attendance->photo_verified ? 'Terverifikasi' : 'Pending' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            <i class="bi bi-exclamation-triangle mr-1"></i>
                                            File hilang
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <i class="bi bi-x-circle mr-1"></i>
                                        Tanpa Foto
                                    </span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-4">
                                @if($attendance->status == 'hadir')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                        Hadir
                                    </span>
                                @elseif($attendance->status == 'izin')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                                        Izin
                                    </span>
                                @elseif($attendance->status == 'alpha')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                        Alpha
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $attendance->status }}
                                    </span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-4">
                                @if($attendance->latitude && $attendance->longitude)
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                            {{ $attendance->distance <= ($school->radius ?? 100) ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                            <i class="bi {{ $attendance->distance <= ($school->radius ?? 100) ? 'bi-check-lg' : 'bi-exclamation-lg' }} mr-2"></i>
                                            {{ $attendance->distance }} m
                                        </span>
                                        <a href="https://maps.google.com/?q={{ $attendance->latitude }},{{ $attendance->longitude }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            <i class="bi bi-geo-alt"></i>
                                            Lihat di Maps
                                        </a>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <i class="bi bi-question-circle mr-1"></i>
                                        Tidak ada lokasi
                                    </span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            class="btn-primary p-2.5 rounded-lg mobile-hover hover-lift"
                                            onclick="showDetailModal('{{ $attendance->id }}', 
                                                '{{ $attendance->user->full_name ?? $attendance->user->username ?? 'Unknown' }}',
                                                '{{ $attendance->user_id }}',
                                                '{{ $attendance->role }}',
                                                '{{ $attendance->date->format('d/m/Y') }}',
                                                '{{ $attendance->time }}',
                                                '{{ $attendance->status }}',
                                                '{{ $attendance->distance }}',
                                                '{{ $attendance->photo_verified }}',
                                                `{{ $attendance->note ?? '' }}`,
                                                '{{ $attendance->latitude }}',
                                                '{{ $attendance->longitude }}',
                                                `{{ $attendance->selfie_photo ? asset('storage/' . $attendance->selfie_photo) : '' }}`)"
                                            title="Detail & Foto">
                                        <i class="bi bi-info-circle mobile-icon-sm"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn-danger p-2.5 rounded-lg mobile-hover hover-lift"
                                            onclick="showDeleteModal('{{ $attendance->id }}', 
                                                '{{ $attendance->user->full_name ?? $attendance->user->username ?? 'Unknown' }}',
                                                '{{ $attendance->date->format('d/m/Y') }}',
                                                '{{ $attendance->time }}',
                                                '{{ $attendance->role }}')"
                                            title="Hapus">
                                        <i class="bi bi-trash mobile-icon-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm text-gray-600">
                    <div>
                        <strong class="text-gray-900">Ringkasan:</strong> 
                        Foto terverifikasi: <span class="font-medium text-gray-900">{{ $attendances->where('photo_verified', true)->count() }}</span> • 
                        Menunggu verifikasi: <span class="font-medium text-gray-900">{{ $attendances->where('selfie_photo', '!=', null)->where('photo_verified', false)->count() }}</span> • 
                        Tanpa foto: <span class="font-medium text-gray-900">{{ $attendances->where('selfie_photo', null)->count() }}</span>
                    </div>
                    <div class="text-xs">
                        Data diperbarui: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="glass-card rounded-xl p-8 sm:p-12 text-center animate-fade-in">
            <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6 border border-gray-200">
                <i class="bi bi-calendar-x text-gray-400 mobile-icon-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data Absensi</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">
                Tidak ditemukan data absensi untuk kriteria yang dipilih. 
                Coba ubah filter atau pilih tanggal yang berbeda.
            </p>
            <a href="{{ route('admin.attendance') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                <i class="bi bi-arrow-clockwise"></i>
                Reset Filter
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto set today's date if not set
    const dateInput = document.getElementById('date');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});

// Function to create modal overlay
function createModalOverlay(content, size = 'max-w-2xl') {
    // Remove any existing modal
    const existingModal = document.getElementById('detailModalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'detailModalOverlay';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in';
    overlay.innerHTML = `
        <div class="${size} w-full max-h-[90vh] overflow-y-auto">
            ${content}
        </div>
    `;
    
    // Add to body
    document.body.appendChild(overlay);
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.remove();
        }
    });
    
    // Add escape key listener
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            overlay.remove();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
    
    return overlay;
}

// Show Detail Modal with Photo
function showDetailModal(id, name, userId, role, date, time, status, distance, isVerified, note, latitude, longitude, photoUrl) {
    // Status badges
    let statusBadge = '';
    if (status === 'hadir') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 border border-green-200">Hadir</span>';
    } else if (status === 'izin') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Izin</span>';
    } else if (status === 'alpha') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800 border border-red-200">Alpha</span>';
    }
    
    // Photo status
    const isVerifiedBool = isVerified === '1' || isVerified === 'true' || isVerified === true;
    const photoStatusBadge = isVerifiedBool ? 
        '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 border border-green-200"><i class="bi bi-check-circle mr-1"></i>Terverifikasi</span>' : 
        '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200"><i class="bi bi-clock mr-1"></i>Belum Diverifikasi</span>';
    
    // Distance color
    const radius = {{ $school->radius ?? 100 }};
    const distanceColor = parseFloat(distance) <= radius ? 'text-green-600' : 'text-red-600';
    const distanceBadge = parseFloat(distance) <= radius ? 
        '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 border border-green-200"><i class="bi bi-check-lg mr-1"></i>' + distance + ' m</span>' :
        '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800 border border-red-200"><i class="bi bi-exclamation-lg mr-1"></i>' + distance + ' m</span>';
    
    // Note section
    const noteSection = note && note.trim() !== '' ? `
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="text-xs text-gray-500 mb-2 font-medium">KETERANGAN</div>
            <div class="text-sm text-gray-900">${note}</div>
        </div>
    ` : '';
    
    // Location section
    let locationSection = '';
    if (latitude && longitude && latitude !== 'null' && longitude !== 'null') {
        const lat = parseFloat(latitude).toFixed(6);
        const lng = parseFloat(longitude).toFixed(6);
        locationSection = `
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="text-xs text-gray-500 mb-2 font-medium">LOKASI ABSENSI</div>
                <div class="text-sm text-gray-900 mb-3">${lat}, ${lng}</div>
                <a href="https://maps.google.com/?q=${latitude},${longitude}" target="_blank" 
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 mobile-hover transition-colors">
                    <i class="bi bi-map"></i>
                    Buka di Google Maps
                </a>
            </div>
        `;
    }
    
    // Photo section
    let photoSection = '';
    if (photoUrl && photoUrl.trim() !== '') {
        photoSection = `
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="text-xs text-gray-500 mb-2 font-medium">FOTO SELFIE</div>
                <div class="rounded-lg overflow-hidden border border-gray-300 bg-gray-100 flex items-center justify-center p-4 mb-4">
                    <img src="${photoUrl}" alt="Foto Selfie" 
                         class="max-w-full max-h-[300px] object-contain rounded-lg" 
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgdmlld0JveD0iMCAwIDMyMCAyNDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMyMCIgaGVpZ2h0PSIyNDAiIGZpbGw9IiNFNUU1RTUiLz48cGF0aCBkPSJNMTYwIDE0MEMxMzQuNCAxNDAgMTE0IDE2MC40IDExNCAxODZDMTE0IDIxMS42IDEzNC40IDIzMiAxNjAgMjMyQzE4NS42IDIzMiAyMDYgMjExLjYgMjA2IDE4NkMyMDYgMTYwLjQgMTg1LjYgMTQwIDE2MCAxNDBaTTI2MCAyMEg2MEM0OC45NTQzIDIwIDQwIDI4Ljk1NDMgNDAgNDBWMjAwQzQwIDIxMS4wNDYgNDguOTU0MyAyMjAgNjAgMjIwSDI2MEMyNzEuMDQ2IDIyMCAyODAgMjExLjA0NiAyODAgMjAwVjQwQzI4MCAyOC45NTQzIDI3MS4wNDYgMjAgMjYwIDIwWiIgZmlsbD0iI0I4QjhCOCIvPjwvc3ZnPg=='">
                </div>
                ${!isVerifiedBool ? `
                    <form action="/admin/verify-photo/${id}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" 
                                class="w-full btn-success px-4 py-2.5 text-sm font-medium flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            Verifikasi Foto Ini
                        </button>
                    </form>
                ` : ''}
            </div>
        `;
    }
    
    // Modal content
    const modalContent = `
        <div class="glass-card rounded-xl overflow-hidden border border-gray-200 shadow-2xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                            <i class="bi bi-calendar-check text-gray-700 mobile-icon-md"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Detail Absensi</h3>
                            <p class="text-xs text-gray-600">ID: ${id}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('detailModalOverlay').remove()" 
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="bi bi-x mobile-icon-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                <!-- User Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center border-2 border-blue-200 shadow-sm">
                            <i class="bi bi-person text-blue-600 mobile-icon-lg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xl font-bold text-gray-900 mb-1">${name}</div>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-blue-800 border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5"></span>
                                    ${role.charAt(0).toUpperCase() + role.slice(1)}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-700 border border-gray-200">
                                    <i class="bi bi-person-badge mr-1.5"></i>
                                    ID: ${userId}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-gray-200 hover-lift">
                        <div class="text-xs text-gray-500 mb-1 font-medium">TANGGAL & WAKTU</div>
                        <div class="text-sm font-bold text-gray-900 mb-1">${date}</div>
                        <div class="text-sm text-gray-600">${time}</div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-gray-200 hover-lift">
                        <div class="text-xs text-gray-500 mb-1 font-medium">STATUS KEHADIRAN</div>
                        <div class="mt-2">${statusBadge}</div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-gray-200 hover-lift">
                        <div class="text-xs text-gray-500 mb-1 font-medium">JARAK DARI SEKOLAH</div>
                        <div class="mt-2">${distanceBadge}</div>
                        <div class="text-xs text-gray-500 mt-1">Batas: ${radius} meter</div>
                    </div>
                </div>
                
                <!-- Photo Status -->
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm font-semibold text-gray-900">VERIFIKASI FOTO</div>
                        <div>${photoStatusBadge}</div>
                    </div>
                    ${photoUrl ? '' : `
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-camera text-gray-400 mobile-icon-lg"></i>
                            </div>
                            <p class="text-sm text-gray-600">Tidak ada foto selfie</p>
                        </div>
                    `}
                </div>
                
                ${photoSection}
                ${noteSection}
                ${locationSection}
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="document.getElementById('detailModalOverlay').remove()" 
                        class="btn-secondary px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i>
                    Tutup
                </button>
                
                <button type="button" 
                        onclick="showDeleteModal('${id}', '${name.replace(/'/g, "\\'")}', '${date}', '${time}', '${role}')"
                        class="btn-danger px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-trash"></i>
                    Hapus Absensi
                </button>
            </div>
        </div>
    `;
    
    createModalOverlay(modalContent, 'max-w-3xl');
}

// Show Delete Modal
function showDeleteModal(id, name, date, time, role) {
    // Remove detail modal if exists
    const detailModal = document.getElementById('detailModalOverlay');
    if (detailModal) {
        detailModal.remove();
    }
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Modal content
    const modalContent = `
        <div class="glass-card rounded-xl overflow-hidden border border-gray-200 shadow-2xl max-w-md">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-pink-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center border border-red-200">
                        <i class="bi bi-exclamation-triangle text-red-600 mobile-icon-md"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hapus Data Absensi</h3>
                        <p class="text-xs text-gray-600">Konfirmasi penghapusan permanen</p>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4 border-2 border-red-100">
                        <i class="bi bi-trash text-red-600 mobile-icon-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Anda yakin ingin menghapus?</h4>
                    <p class="text-sm text-gray-600 mb-6">
                        Data yang dihapus tidak dapat dikembalikan. Pastikan ini adalah data yang benar.
                    </p>
                    
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6">
                        <div class="text-center mb-3">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto border border-gray-200 mb-2">
                                <i class="bi bi-person text-gray-600"></i>
                            </div>
                            <div class="font-bold text-gray-900">${name}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="text-left">
                                <div class="text-xs text-gray-500">Tanggal</div>
                                <div class="font-medium text-gray-900">${date}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">Waktu</div>
                                <div class="font-medium text-gray-900">${time}</div>
                            </div>
                            <div class="text-left">
                                <div class="text-xs text-gray-500">Role</div>
                                <div class="font-medium text-gray-900">${role.charAt(0).toUpperCase() + role.slice(1)}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">ID Absensi</div>
                                <div class="font-medium text-gray-900">${id.substring(0, 8)}...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="document.getElementById('detailModalOverlay').remove()" 
                        class="btn-secondary px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i>
                    Batalkan
                </button>
                
                <form action="/admin/attendance/${id}" method="POST" class="contents">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" 
                            class="btn-danger px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                        <i class="bi bi-trash"></i>
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    `;
    
    createModalOverlay(modalContent, 'max-w-md');
}
</script>
@endpush