@extends('layouts.app')

@section('title', 'Pengaturan Waktu Absen')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6 md:py-8">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                    <i class="bi bi-clock mr-2"></i>
                    Pengaturan Waktu Absen
                </h1>
                <p class="text-sm text-gray-600">
                    Kelola dan atur waktu absensi untuk setiap tipe kehadiran
                </p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="btn-secondary inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Time Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Server Time Card -->
        <div class="glass-card rounded-xl p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center border border-blue-200">
                    <i class="bi bi-clock text-blue-600 mobile-icon-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900 mb-1">Waktu Server</h3>
                    <div id="serverTime" class="text-2xl font-bold text-gray-900 mb-1">
                        {{ now()->format('H:i:s') }}
                    </div>
                    <p class="text-sm text-gray-600">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Status Card -->
        <div class="glass-card rounded-xl p-5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center border border-green-200">
                    <i class="bi bi-calendar-check text-green-600 mobile-icon-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900 mb-1">Status Absensi</h3>
                    <div id="attendanceStatusInfo" class="text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-500 animate-pulse"></div>
                            <span class="text-gray-600">Memeriksa waktu absen...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Time Setting Form -->
    <div class="glass-card rounded-xl p-5 mb-6 animate-fade-in">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-plus-circle text-green-600 mobile-icon-md"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tambah Waktu Absen Baru</h2>
                <p class="text-sm text-gray-600">Buat pengaturan waktu absen sesuai kebutuhan</p>
            </div>
        </div>

        <form action="{{ route('admin.time.settings.create') }}" method="POST" id="timeSettingForm">
            @csrf
            
            <div class="space-y-5">
                <!-- Name and Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Waktu Absen
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               placeholder="Contoh: Absen Masuk Pagi"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Nama yang mudah dipahami</p>
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Absen
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-tag text-gray-400"></i>
                            </div>
                            <select id="type" 
                                    name="type"
                                    class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm appearance-none"
                                    required>
                                <option value="masuk">Masuk</option>
                                <option value="pulang">Pulang</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="bi bi-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Mulai
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-clock text-gray-400"></i>
                            </div>
                            <input type="time" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="07:00"
                                   class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                                   required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Berakhir
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-clock text-gray-400"></i>
                            </div>
                            <input type="time" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="08:00"
                                   class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Set 00:00 untuk waktu tengah malam</p>
                    </div>
                </div>

                <!-- Days of Week -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Hari Berlaku
                    </label>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3 mb-3">
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
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="day{{ $key }}" 
                                   name="days_of_week[]" 
                                   value="{{ $key }}"
                                   {{ in_array($key, [1,2,3,4,5]) ? 'checked' : '' }}
                                   class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
                            <label for="day{{ $key }}" class="ml-2 text-sm text-gray-700">
                                {{ $day }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               id="select_all_days"
                               class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
                        <label for="select_all_days" class="text-sm text-gray-600">
                            Pilih Semua Hari
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika berlaku setiap hari</p>
                </div>

                <!-- Status and Description -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       checked
                                       class="relative w-11 h-6 rounded-full appearance-none border border-gray-300 bg-gray-200 checked:bg-black checked:border-black transition-colors duration-300 toggle-switch">
                                <label for="is_active" class="ml-3 text-sm text-gray-700">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan (Opsional)
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="2"
                                  placeholder="Contoh: Waktu absen khusus untuk ujian"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"></textarea>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-info-circle text-blue-600 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900 mb-1">Petunjuk Pengaturan</h4>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li>• Waktu absen bisa untuk jam berapa saja (tidak hanya jam 7)</li>
                                <li>• Admin bisa setting multiple time slots</li>
                                <li>• Bisa atur hari tertentu saja (misal: Senin-Jumat)</li>
                                <li>• Bisa dinonaktifkan sementara tanpa menghapus</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit" class="btn-success flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium">
                        <i class="bi bi-save"></i>
                        Simpan Waktu Absen
                    </button>
                    <button type="button" onclick="resetForm()" class="btn-secondary flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset Form
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Time Settings List -->
    <div class="glass-card rounded-xl overflow-hidden animate-fade-in">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                        <i class="bi bi-list-check text-gray-600 mobile-icon-md"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Daftar Waktu Absen</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Total {{ $settings->count() }} pengaturan waktu
                        </p>
                    </div>
                </div>
                
                <div class="text-sm text-gray-600">
                    <span class="font-bold text-gray-900">{{ $settings->where('is_active', true)->count() }}</span> aktif
                    <span class="mx-2">•</span>
                    <span class="font-bold text-gray-900">{{ $settings->where('is_active', false)->count() }}</span> nonaktif
                </div>
            </div>
        </div>

        @if($settings->count() > 0)
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama & Keterangan</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Hari</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tipe</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Digunakan</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($settings as $setting)
                            @php
                                // Parse days_of_week
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
                                    if (strlen($daysDisplay) > 30) {
                                        $daysDisplay = substr($daysDisplay, 0, 30) . '...';
                                    }
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
                            
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150 {{ !$setting->is_active ? 'bg-gray-50/30' : '' }}">
                                <td class="py-4 px-4 text-sm text-gray-900 font-medium text-center">{{ $loop->iteration }}</td>
                                
                                <!-- Nama & Keterangan -->
                                <td class="py-4 px-4">
                                    <div>
                                        <div class="font-medium text-gray-900 flex items-center gap-2">
                                            {{ $setting->name }}
                                            @if(!$setting->is_active)
                                                <span class="text-xs text-gray-500">(Nonaktif)</span>
                                            @endif
                                        </div>
                                        @if($setting->description)
                                            <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">
                                                {{ $setting->description }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Waktu -->
                                <td class="py-4 px-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $setting->start_time->format('H:i') }} - {{ $setting->end_time->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $setting->start_time->diff($setting->end_time)->format('%H jam %I menit') }}
                                    </div>
                                    @if($isActiveToday)
                                        <div class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                            <i class="bi bi-check-circle"></i>
                                            Berlaku hari ini
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <i class="bi bi-x-circle"></i>
                                            Tidak berlaku hari ini
                                        </div>
                                    @endif
                                </td>
                                
                                <!-- Hari -->
                                <td class="py-4 px-4">
                                    <div class="text-sm text-gray-900">{{ $daysDisplay }}</div>
                                </td>
                                
                                <!-- Tipe -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                        {{ $setting->type == 'masuk' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                                           ($setting->type == 'pulang' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                           'bg-yellow-100 text-yellow-800 border border-yellow-200') }}">
                                        <i class="bi {{ $setting->type == 'masuk' ? 'bi-door-open' : ($setting->type == 'pulang' ? 'bi-door-closed' : 'bi-clock') }} mr-2"></i>
                                        {{ ucfirst($setting->type) }}
                                    </span>
                                </td>
                                
                                <!-- Status -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                        {{ $setting->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                                        <span class="w-2 h-2 rounded-full mr-2 {{ $setting->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                        {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                
                                <!-- Digunakan -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="bi bi-people mr-2"></i>
                                            {{ $usageCount }} kali
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Aksi -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                class="btn-primary p-2.5 rounded-lg mobile-hover hover-lift"
                                                onclick="showEditModal({{ json_encode($setting) }}, {{ json_encode($currentDaysArray) }})"
                                                title="Edit">
                                            <i class="bi bi-pencil mobile-icon-sm"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        @if($usageCount == 0)
                                            <button type="button" 
                                                    class="btn-danger p-2.5 rounded-lg mobile-hover hover-lift"
                                                    onclick="showDeleteModal('{{ $setting->id }}', '{{ addslashes($setting->name) }}')"
                                                    title="Hapus">
                                                <i class="bi bi-trash mobile-icon-sm"></i>
                                            </button>
                                        @else
                                            <span class="w-9 h-9 flex items-center justify-center text-gray-400" 
                                                  title="Tidak dapat dihapus karena sudah digunakan">
                                                <i class="bi bi-lock mobile-icon-sm"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm text-gray-600">
                    <div>
                        <div class="font-medium text-gray-900">Total Setting</div>
                        <div class="text-lg font-bold text-gray-900 mt-1">{{ $settings->count() }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Aktif</div>
                        <div class="text-lg font-bold text-green-600 mt-1">{{ $settings->where('is_active', true)->count() }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Tipe Masuk</div>
                        <div class="text-lg font-bold text-blue-600 mt-1">{{ $settings->where('type', 'masuk')->count() }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Tipe Pulang</div>
                        <div class="text-lg font-bold text-yellow-600 mt-1">{{ $settings->where('type', 'pulang')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="p-8 sm:p-12 text-center">
                <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6 border border-gray-200">
                    <i class="bi bi-clock text-gray-400 mobile-icon-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada pengaturan waktu</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">
                    Tambahkan pengaturan waktu absen menggunakan form di atas untuk mengatur jadwal absensi.
                </p>
            </div>
        @endif
    </div>

    <!-- Info Panel -->
    <div class="glass-card rounded-xl p-5 mt-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-info-circle text-blue-600 mobile-icon-md"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Informasi Sistem</h3>
                <p class="text-sm text-gray-600">Cara kerja pengaturan waktu absen</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="bi bi-check-circle text-green-600"></i>
                    <span>Sistem akan memvalidasi waktu absen berdasarkan setting aktif</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="bi bi-check-circle text-green-600"></i>
                    <span>User hanya bisa absen pada waktu yang sudah ditentukan</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="bi bi-check-circle text-green-600"></i>
                    <span>Bisa atur multiple time slots untuk fleksibilitas</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="bi bi-check-circle text-green-600"></i>
                    <span>Nonaktifkan setting tanpa menghapus untuk sementara</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF Token untuk AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Update server time every second
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

// Check attendance status
function checkAttendanceStatus() {
    $.ajax({
        url: '{{ route("check.attendance.time") }}',
        method: 'POST',
        data: {
            _token: csrfToken
        },
        success: function(response) {
            const statusDiv = document.getElementById('attendanceStatusInfo');
            
            if (response.allowed) {
                statusDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-green-600 font-medium">ABSENSI DIJINKAN</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">${response.message}</p>
                    <div class="text-xs text-gray-500 mt-1">Waktu: ${response.current_time}</div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-red-600 font-medium">ABSENSI DITOLAK</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">${response.message}</p>
                    <div class="text-xs text-gray-500 mt-1">Waktu: ${response.current_time}</div>
                `;
            }
        },
        error: function() {
            document.getElementById('attendanceStatusInfo').innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <span class="text-yellow-600">Gagal memeriksa status</span>
                </div>
            `;
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
    
    // Reset toggle switch
    const toggle = document.getElementById('is_active');
    if (toggle) {
        toggle.checked = true;
    }
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

// Function to create modal overlay
function createModalOverlay(content, size = 'max-w-2xl') {
    // Remove any existing modal
    const existingModal = document.getElementById('modalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'modalOverlay';
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

// Show Edit Modal
function showEditModal(setting, daysArray = []) {
    console.log('Setting data:', setting);
    console.log('Setting is_active:', setting.is_active, 'Type:', typeof setting.is_active);
    
    // Format waktu untuk input time
    const formatTime = (timeStr) => {
        if (!timeStr) return '';
        // Jika sudah format HH:mm
        if (typeof timeStr === 'string' && timeStr.includes(':')) {
            return timeStr;
        }
        // Jika Date object atau timestamp
        try {
            const time = new Date(timeStr);
            return time.toTimeString().slice(0, 5);
        } catch (e) {
            return timeStr;
        }
    };
    
    // Build days checkboxes
    const days = {
        1: 'Senin',
        2: 'Selasa', 
        3: 'Rabu',
        4: 'Kamis',
        5: 'Jumat',
        6: 'Sabtu',
        7: 'Minggu'
    };
    
    let daysCheckboxes = '';
    for (const [key, day] of Object.entries(days)) {
        const isChecked = Array.isArray(daysArray) && daysArray.includes(parseInt(key));
        daysCheckboxes += `
            <div class="flex items-center">
                <input type="checkbox" 
                       id="edit_day_${key}_${setting.id}" 
                       name="days_of_week[]" 
                       value="${key}" 
                       ${isChecked ? 'checked' : ''}
                       class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
                <label for="edit_day_${key}_${setting.id}" class="ml-2 text-sm text-gray-700">
                    ${day}
                </label>
            </div>
        `;
    }
    
    // Fix untuk toggle switch - pastikan nilai boolean benar
    const isActiveValue = setting.is_active == 1 || setting.is_active === true ? 1 : 0;
    const isActiveChecked = isActiveValue == 1 ? 'checked' : '';
    
    console.log('Parsed is_active:', isActiveValue, 'Checked:', isActiveChecked);
    
    // Modal content
    const modalContent = `
        <div class="glass-card rounded-xl overflow-hidden border border-gray-200 shadow-2xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center border border-blue-200">
                            <i class="bi bi-pencil text-blue-600 mobile-icon-md"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Edit Waktu Absen</h3>
                            <p class="text-xs text-gray-600">ID: ${setting.id}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('modalOverlay').remove()" 
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-blue-100 text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="bi bi-x mobile-icon-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <form id="editForm${setting.id}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label for="edit_name_${setting.id}" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Waktu Absen
                            </label>
                            <input type="text" 
                                   id="edit_name_${setting.id}" 
                                   name="name" 
                                   value="${setting.name.replace(/"/g, '&quot;')}"
                                   placeholder="Contoh: Absen Masuk Pagi"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                                   required>
                        </div>
                        
                        <!-- Time Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_start_time_${setting.id}" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Mulai
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-clock text-gray-400"></i>
                                    </div>
                                    <input type="time" 
                                           id="edit_start_time_${setting.id}" 
                                           name="start_time" 
                                           value="${formatTime(setting.start_time)}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                                           required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="edit_end_time_${setting.id}" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Berakhir
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-clock text-gray-400"></i>
                                    </div>
                                    <input type="time" 
                                           id="edit_end_time_${setting.id}" 
                                           name="end_time" 
                                           value="${formatTime(setting.end_time)}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Type -->
                        <div>
                            <label for="edit_type_${setting.id}" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Absen
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-tag text-gray-400"></i>
                                </div>
                                <select id="edit_type_${setting.id}" 
                                        name="type"
                                        class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm appearance-none"
                                        required>
                                    <option value="masuk" ${setting.type === 'masuk' ? 'selected' : ''}>Masuk</option>
                                    <option value="pulang" ${setting.type === 'pulang' ? 'selected' : ''}>Pulang</option>
                                    <option value="lainnya" ${setting.type === 'lainnya' ? 'selected' : ''}>Lainnya</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="bi bi-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Days of Week -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Hari Berlaku
                            </label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3 mb-3">
                                ${daysCheckboxes}
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <input type="checkbox" 
                                       id="edit_select_all_${setting.id}"
                                       class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded"
                                       onclick="toggleAllDays(${setting.id})">
                                <label for="edit_select_all_${setting.id}" class="text-sm text-gray-600">
                                    Pilih Semua Hari
                                </label>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label for="edit_description_${setting.id}" class="block text-sm font-medium text-gray-700 mb-2">
                                Keterangan (Opsional)
                            </label>
                            <textarea id="edit_description_${setting.id}" 
                                      name="description" 
                                      rows="2"
                                      placeholder="Contoh: Waktu absen khusus untuk ujian"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent bg-white text-sm">${setting.description || ''}</textarea>
                        </div>
                        
                        <!-- Status - SIMPLIFIED VERSION -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Status
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center">
                                    <!-- Toggle switch yang sebenarnya -->
                                    <input type="checkbox" 
                                           id="edit_is_active_${setting.id}" 
                                           name="is_active" 
                                           value="1"
                                           ${isActiveChecked}
                                           class="toggle-switch relative w-11 h-6 rounded-full appearance-none border border-gray-300 bg-gray-200 checked:bg-black checked:border-black transition-colors duration-300">
                                    <label for="edit_is_active_${setting.id}" class="ml-3 text-sm text-gray-700">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form validation -->
                        <div class="hidden" id="editFormMessages${setting.id}"></div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="document.getElementById('modalOverlay').remove()" 
                        class="btn-secondary px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i>
                    Batal
                </button>
                
                <button type="button" 
                        onclick="submitEditFormV2(${setting.id})"
                        class="btn-primary px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    `;
    
    createModalOverlay(modalContent, 'max-w-2xl');
    
    // Setelah modal dibuat, inisialisasi toggle switch
    setTimeout(() => {
        initToggleSwitch(setting.id);
    }, 100);
}

// Inisialisasi toggle switch
function initToggleSwitch(settingId) {
    const toggle = document.getElementById(`edit_is_active_${settingId}`);
    if (toggle) {
        // Update styling berdasarkan status
        if (toggle.checked) {
            toggle.classList.add('checked:bg-black', 'checked:border-black');
            toggle.classList.remove('bg-gray-200', 'border-gray-300');
        } else {
            toggle.classList.remove('checked:bg-black', 'checked:border-black');
            toggle.classList.add('bg-gray-200', 'border-gray-300');
        }
    }
}

// Toggle all days in edit modal
function toggleAllDays(settingId) {
    const selectAll = document.getElementById(`edit_select_all_${settingId}`);
    const checkboxes = document.querySelectorAll(`#editForm${settingId} input[name="days_of_week[]"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Submit edit form via AJAX - NEW FIXED VERSION
// Submit edit form via AJAX - NEW FIXED VERSION
function submitEditFormV2(settingId) {
    console.log('Submitting form for setting:', settingId);
    
    const form = document.getElementById(`editForm${settingId}`);
    if (!form) {
        alert('Form tidak ditemukan!');
        return;
    }
    
    // Build FormData
    const formData = new FormData();
    
    // Get form values
    const name = document.getElementById(`edit_name_${settingId}`).value;
    const startTime = document.getElementById(`edit_start_time_${settingId}`).value;
    const endTime = document.getElementById(`edit_end_time_${settingId}`).value;
    const type = document.getElementById(`edit_type_${settingId}`).value;
    const description = document.getElementById(`edit_description_${settingId}`).value;
    const isActiveCheckbox = document.getElementById(`edit_is_active_${settingId}`);
    
    // FIX: Pastikan kita mengirim '0' jika checkbox tidak dicentang
    const isActive = isActiveCheckbox.checked ? '1' : '0';
    
    console.log('isActive value to send:', isActive, 'Checked:', isActiveCheckbox.checked);
    
    // Validasi
    if (!name || !startTime || !endTime || !type) {
        alert('Harap isi semua field yang wajib diisi!');
        return false;
    }
    
    if (startTime && endTime && endTime !== '00:00') {
        if (startTime >= endTime) {
            alert('Waktu mulai harus lebih awal dari waktu berakhir!');
            return false;
        }
    }
    
    // Tambahkan data ke FormData - SELALU TAMBAHKAN is_active
    formData.append('_token', csrfToken);
    formData.append('_method', 'PUT');
    formData.append('name', name);
    formData.append('start_time', startTime);
    formData.append('end_time', endTime);
    formData.append('type', type);
    formData.append('description', description);
    formData.append('is_active', isActive); // SELALU dikirim, '0' atau '1'
    
    // Tambahkan days_of_week
    const dayCheckboxes = document.querySelectorAll(`#editForm${settingId} input[name="days_of_week[]"]:checked`);
    dayCheckboxes.forEach((checkbox, index) => {
        formData.append(`days_of_week[${index}]`, checkbox.value);
    });
    
    // Debug: Log data yang akan dikirim
    console.log('Data to send:');
    for (let [key, value] of formData.entries()) {
        console.log(key, ':', value);
    }
    
    // Show loading
    const submitBtn = document.querySelector('#modalOverlay .btn-primary');
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Menyimpan...</span>
            </div>
        `;
        submitBtn.disabled = true;
    }
    
    // Update URL untuk route
    const updateUrl = `/admin/attendance-time-settings/${settingId}`;
    
    // Submit via AJAX
    fetch(updateUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (response.redirected) {
            console.log('Redirected to:', response.url);
            if (response.url.includes('/login')) {
                alert('Session Anda telah habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            window.location.href = response.url;
            return;
        }
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response:', text);
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 100)}`);
                }
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        
        if (data && data.success) {
            // Show success message
            const messageDiv = document.getElementById(`editFormMessages${settingId}`);
            if (messageDiv) {
                messageDiv.className = 'bg-green-50 border border-green-200 rounded-xl p-4 mb-4';
                messageDiv.innerHTML = `
                    <div class="flex items-center gap-3">
                        <i class="bi bi-check-circle text-green-600"></i>
                        <div>
                            <h4 class="text-sm font-medium text-green-900">Berhasil!</h4>
                            <p class="text-xs text-green-800">${data.message || 'Pengaturan waktu berhasil diperbarui.'}</p>
                        </div>
                    </div>
                `;
            }
            
            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data?.message || 'Terjadi kesalahan tanpa pesan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(`Gagal menyimpan: ${error.message}`);
        
        // Reset button
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Show Delete Modal
function showDeleteModal(settingId, name) {
    const modalContent = `
        <div class="glass-card rounded-xl overflow-hidden border border-gray-200 shadow-2xl max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-pink-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center border border-red-200">
                        <i class="bi bi-exclamation-triangle text-red-600 mobile-icon-md"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hapus Pengaturan Waktu</h3>
                        <p class="text-xs text-gray-600">Konfirmasi penghapusan permanen</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4 border-2 border-red-100">
                        <i class="bi bi-trash text-red-600 mobile-icon-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Hapus "${name}"?</h4>
                    <p class="text-sm text-gray-600 mb-6">
                        Data yang dihapus tidak dapat dikembalikan. Pastikan ini adalah data yang benar.
                    </p>
                    
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto border border-gray-200 mb-2">
                                <i class="bi bi-clock text-gray-600"></i>
                            </div>
                            <div class="font-bold text-gray-900">${name}</div>
                            <div class="text-xs text-gray-500 mt-1">ID: ${settingId}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="document.getElementById('modalOverlay').remove()" 
                        class="btn-secondary px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i>
                    Batalkan
                </button>
                
                <form action="/admin/attendance-time-settings/${settingId}" method="POST" class="contents">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" 
                            class="btn-danger px-4 py-2.5 text-sm font-medium flex-1 flex items-center justify-center gap-2">
                        <i class="bi bi-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    `;
    
    createModalOverlay(modalContent, 'max-w-md');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateServerTime();
    setInterval(updateServerTime, 1000);
    
    checkAttendanceStatus();
    setInterval(checkAttendanceStatus, 30000);
    
    setInterval(checkAttendanceStatus, 10000);
});
</script>
@endpush