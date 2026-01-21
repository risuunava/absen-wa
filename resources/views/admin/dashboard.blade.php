@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600 text-sm mt-1">Sistem Absensi Sekolah - Panel Administrasi</p>
        </div>
        <div class="text-sm text-gray-500">
            <span class="font-medium">{{ date('d F Y') }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-xl p-6 border border-gray-100 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50">
                    <i class="bi bi-people text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $totalMurid }}</h3>
            <p class="text-sm text-gray-600">Murid Terdaftar</p>
        </div>
        
        <div class="glass-card rounded-xl p-6 border border-gray-100 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                    <i class="bi bi-person-badge text-emerald-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $totalGuru }}</h3>
            <p class="text-sm text-gray-600">Guru Terdaftar</p>
        </div>
        
        <div class="glass-card rounded-xl p-6 border border-gray-100 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-purple-50 to-pink-50">
                    <i class="bi bi-calendar-check text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Hari Ini</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $absensiHariIni }}</h3>
            <p class="text-sm text-gray-600">Absensi Hari Ini</p>
        </div>
        
        <div class="glass-card rounded-xl p-6 border border-gray-100 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-amber-50 to-yellow-50">
                    <i class="bi bi-check-circle text-amber-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Valid</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $absensiValid }}</h3>
            <p class="text-sm text-gray-600">Absensi Valid</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- School Configuration -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="bi bi-geo-alt text-gradient mr-2"></i>
                    Konfigurasi Lokasi Sekolah
                </h2>
            </div>
            
            <form action="{{ route('admin.school.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="latitude" class="text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" 
                               step="any" 
                               id="latitude" 
                               name="latitude" 
                               value="{{ $school->latitude ?? '-6.2088' }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="longitude" class="text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" 
                               step="any" 
                               id="longitude" 
                               name="longitude" 
                               value="{{ $school->longitude ?? '106.8456' }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="radius" class="text-sm font-medium text-gray-700">Radius Absensi (meter)</label>
                    <input type="number" 
                           id="radius" 
                           name="radius" 
                           value="{{ $school->radius ?? 100 }}" 
                           min="10" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                    <p class="text-xs text-gray-500">Jarak maksimal dari sekolah untuk absensi valid</p>
                </div>
                
                <button type="submit" class="w-full flex items-center justify-center space-x-2 gradient-bg text-black py-3 rounded-xl hover:opacity-90 transition-opacity text-sm font-semibold hover-lift">
                    <i class="bi bi-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </form>
        </div>
        
        <!-- Quick Actions -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="bi bi-lightning text-gradient mr-2"></i>
                    Quick Actions
                </h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.users') }}?role=murid" class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift">
                    <i class="bi bi-people text-blue-600"></i>
                    <span class="text-sm font-medium">Kelola Murid</span>
                </a>
                
                <a href="{{ route('admin.users') }}?role=guru" class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift">
                    <i class="bi bi-person-badge text-emerald-600"></i>
                    <span class="text-sm font-medium">Kelola Guru</span>
                </a>
                
                <a href="{{ route('admin.attendance') }}?role=murid&date={{ date('Y-m-d') }}" 
                   class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift">
                    <i class="bi bi-calendar-check text-purple-600"></i>
                    <span class="text-sm font-medium">Lihat Absensi</span>
                </a>
                
                <a href="{{ route('admin.time.settings') }}" class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift">
                    <i class="bi bi-clock text-amber-600"></i>
                    <span class="text-sm font-medium">Kelola Waktu</span>
                </a>

                <!-- Tambahkan tombol Import User -->
                <button type="button" 
                        onclick="openImportModal('murid')"
                        class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift cursor-pointer">
                    <i class="bi bi-file-earmark-excel text-green-600"></i>
                    <span class="text-sm font-medium">Import Murid (CSV)</span>
                </button>

                <button type="button" 
                        onclick="openImportModal('guru')"
                        class="flex items-center justify-center space-x-2 p-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors hover-lift cursor-pointer">
                    <i class="bi bi-file-earmark-excel text-orange-600"></i>
                    <span class="text-sm font-medium">Import Guru (CSV)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Time Settings -->
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-clock-history text-gradient mr-2"></i>
                Waktu Absen Aktif
            </h2>
            <span class="text-xs font-medium px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                {{ $timeSettings->count() }} waktu aktif
            </span>
        </div>
        
        @if($timeSettings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($timeSettings as $setting)
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover-lift">
                        <div class="text-center">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">{{ $setting->name }}</h5>
                            <div class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $setting->start_time->format('H:i') }} - {{ $setting->end_time->format('H:i') }}
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium 
                                {{ $setting->type == 'masuk' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ ucfirst($setting->type) }}
                            </span>
                            <div class="mt-3">
                                <small class="text-gray-500 text-xs">
                                    Durasi: {{ $setting->start_time->diffInHours($setting->end_time) }} jam
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-gray-700">Masuk: {{ $timeSettings->where('type', 'masuk')->count() }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-gray-700">Pulang: {{ $timeSettings->where('type', 'pulang')->count() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.time.settings') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        Kelola Waktu →
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-clock text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-700 font-medium mb-2">Belum ada waktu absen aktif</h4>
                <p class="text-gray-500 text-sm mb-4">Tambahkan waktu absen untuk mengaktifkan sistem</p>
                <a href="{{ route('admin.time.settings') }}" class="inline-flex items-center space-x-2 gradient-bg text-white px-4 py-2 rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Waktu Absen</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Attendances -->
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-clock-history text-gradient mr-2"></i>
                Absensi Terbaru
            </h2>
            <a href="{{ route('admin.attendance') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentAttendances as $attendance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $attendance->user->full_name ?? $attendance->user->username }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $attendance->role === 'murid' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($attendance->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $attendance->date->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $attendance->time }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $attendance->attendance_type === 'masuk' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($attendance->attendance_type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ number_format($attendance->distance, 0) }} m</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ strtolower($attendance->status) === 'valid' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center">
                                <div class="text-gray-500">Belum ada data absensi</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Current Time Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="bi bi-clock text-gradient mr-2"></i>
                Waktu Server Saat Ini
            </h3>
            <div class="text-center">
                <div id="serverTime" class="text-4xl font-bold text-gray-900 mb-2">{{ now()->format('H:i:s') }}</div>
                <p class="text-gray-600 text-sm">{{ now()->format('d F Y') }}</p>
            </div>
        </div>
        
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="bi bi-calendar-check text-gradient mr-2"></i>
                Status Absensi Hari Ini
            </h3>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4">
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $absensiHariIni }}</div>
                    <div class="text-xs text-gray-600 font-medium">Total Absensi</div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4">
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $absensiValid }}</div>
                    <div class="text-xs text-gray-600 font-medium">Absensi Valid</div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <small class="text-gray-500 text-xs">Terakhir update: {{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import User -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-file-earmark-excel text-green-600 mr-2"></i>
                Import Data User (CSV)
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="importForm" action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <label class="text-sm font-medium text-gray-700">Role User</label>
                <div id="roleDisplay" class="text-lg font-semibold text-gray-900"></div>
                <input type="hidden" id="importRole" name="role" value="">
            </div>
            
            <div class="space-y-2">
                <label for="importFile" class="text-sm font-medium text-gray-700">File CSV</label>
                <input type="file" 
                       id="importFile" 
                       name="file" 
                       accept=".csv,.txt" 
                       required
                       class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                <p class="text-xs text-gray-500">
                    Format: .csv (Comma Separated Values). Maksimal 2MB.
                    <br>
                    Kolom wajib: username, phone, password, full_name, class, subject
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                    <i class="bi bi-info-circle mr-2"></i>
                    Format CSV:
                </h4>
                <div class="text-xs text-blue-700">
                    <code class="block bg-blue-100 p-2 rounded mb-2">
                        username,phone,password,full_name,class,subject
                    </code>
                    <ul class="space-y-1">
                        <li>• Baris pertama adalah header (wajib)</li>
                        <li>• <strong>class:</strong> Wajib untuk murid, kosong untuk guru</li>
                        <li>• <strong>subject:</strong> Wajib untuk guru, kosong untuk murid</li>
                        <li>• Password akan di-hash otomatis</li>
                    </ul>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.users.download-template') }}" 
                   class="flex-1 flex items-center justify-center space-x-2 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium">
                    <i class="bi bi-download"></i>
                    <span>Download Template</span>
                </a>
                
                <button type="submit" class="flex-1 flex items-center justify-center space-x-2 gradient-bg text-black py-3 rounded-xl hover:opacity-90 transition-opacity text-sm font-semibold">
                    <i class="bi bi-upload"></i>
                    <span>Import Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Error Import -->
@if(session()->has('import_errors'))
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                Detail Error Import
            </h3>
            <button type="button" onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="space-y-4 max-h-96 overflow-y-auto">
            @foreach(session('import_errors') as $error)
            <div class="border border-red-200 rounded-xl p-4 bg-red-50">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="text-sm font-semibold text-red-800">Baris {{ $error['row_number'] }}</h4>
                    <span class="text-xs text-red-600">Gagal</span>
                </div>
                
                <div class="text-xs text-gray-700 mb-2">
                    <strong>Data:</strong>
                    @php
                        $headers = ['username', 'phone', 'password', 'full_name', 'class', 'subject'];
                    @endphp
                    @foreach($error['row'] as $index => $value)
                        @if(isset($headers[$index]))
                            <span class="mr-2">{{ $headers[$index] }}: <code>{{ $value }}</code></span>
                        @endif
                    @endforeach
                </div>
                
                <div class="text-xs text-red-600">
                    <strong>Error:</strong>
                    <ul class="list-disc pl-4 mt-1">
                        @foreach($error['errors'] as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeErrorModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors text-sm font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function closeErrorModal() {
        document.getElementById('errorModal').style.display = 'none';
    }
    
    // Tampilkan modal error secara otomatis
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'flex';
        }
    });
</script>
@endif

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
        
        document.getElementById('serverTime').textContent = timeString;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateServerTime();
        setInterval(updateServerTime, 1000);
    });

    function openImportModal(role) {
        const modal = document.getElementById('importModal');
        const roleDisplay = document.getElementById('roleDisplay');
        const importRole = document.getElementById('importRole');
        
        if (role === 'murid') {
            roleDisplay.textContent = 'Murid';
        } else {
            roleDisplay.textContent = 'Guru';
        }
        
        importRole.value = role;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target.id === 'importModal') {
            closeImportModal();
        }
    });

    // Handle form submission
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('importFile');
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert('Silakan pilih file terlebih dahulu.');
            return;
        }
        
        if (fileInput.files[0].size > maxSize) {
            e.preventDefault();
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            return;
        }
        
        // Show loading indicator
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Memproses...</span>';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection