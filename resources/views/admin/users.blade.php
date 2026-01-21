@extends('layouts.app')

@section('title', 'Kelola ' . ucfirst($role))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-{{ $role === 'murid' ? 'people' : 'person-badge' }} text-gray-900"></i>
                Kelola {{ ucfirst($role) }}
            </h1>
            <p class="text-gray-600 text-sm mt-1">
                Sistem Absensi Sekolah - Manajemen Data {{ ucfirst($role) }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium mobile-hover">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
            
            <button type="button" 
                    onclick="openImportModal('{{ $role }}')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold mobile-hover">
                <i class="bi bi-file-earmark-excel"></i>
                <span>Import CSV</span>
            </button>
            
            <button type="button" 
                    onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 gradient-bg text-black px-4 py-2 rounded-lg hover:opacity-90 transition-opacity text-sm font-semibold mobile-hover">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah {{ ucfirst($role) }}</span>
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card rounded-xl p-5 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100">
                    <i class="bi bi-people text-gray-700"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $users->count() }}</h3>
            <p class="text-sm text-gray-600">
                {{ ucfirst($role) }} Terdaftar
            </p>
        </div>
        
        <div class="glass-card rounded-xl p-5 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100">
                    <i class="bi bi-calendar-check text-gray-700"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Hari Ini</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">
                {{ \App\Models\Attendance::where('role', $role)->whereDate('date', \Carbon\Carbon::today())->count() }}
            </h3>
            <p class="text-sm text-gray-600">
                {{ ucfirst($role) }} Absen Hari Ini
            </p>
        </div>
        
        <div class="glass-card rounded-xl p-5 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100">
                    <i class="bi bi-check-circle text-gray-700"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Valid</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">
                {{ \App\Models\Attendance::where('role', $role)->whereDate('date', \Carbon\Carbon::today())->where('status', 'VALID')->count() }}
            </h3>
            <p class="text-sm text-gray-600">Absensi Valid</p>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-5 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto glass-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-person-plus"></i>
                    Tambah {{ ucfirst($role) }} Baru
                </h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 mobile-hover">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form id="createForm" action="{{ route('admin.users.create') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">
                
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    <p class="text-xs text-gray-500">Username unik untuk login</p>
                </div>
                
                <div class="space-y-2">
                    <label for="phone" class="text-sm font-medium text-gray-700">Nomor HP *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    <p class="text-xs text-gray-500">Nomor telepon aktif</p>
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    <p class="text-xs text-gray-500">Minimal 6 karakter</p>
                </div>
                
                <div class="space-y-2">
                    <label for="full_name" class="text-sm font-medium text-gray-700">Nama Lengkap *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                </div>
                
                @if($role === 'murid')
                    <div class="space-y-2">
                        <label for="class" class="text-sm font-medium text-gray-700">Kelas *</label>
                        <input type="text" 
                               id="class" 
                               name="class" 
                               required
                               placeholder="Contoh: XII IPA 1"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    </div>
                @else
                    <div class="space-y-2">
                        <label for="subject" class="text-sm font-medium text-gray-700">Mata Pelajaran *</label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               required
                               placeholder="Contoh: Matematika"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    </div>
                @endif
                
                <div class="flex items-center gap-3 pt-4">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium mobile-hover">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 gradient-bg text-black py-2.5 rounded-lg hover:opacity-90 transition-opacity text-sm font-semibold mobile-hover">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-5 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto glass-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    Edit {{ ucfirst($role) }}
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 mobile-hover">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                
                <div class="space-y-2">
                    <label for="edit_username" class="text-sm font-medium text-gray-700">Username</label>
                    <input type="text" 
                           id="edit_username" 
                           name="username" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                </div>
                
                <div class="space-y-2">
                    <label for="edit_phone" class="text-sm font-medium text-gray-700">Nomor HP</label>
                    <input type="tel" 
                           id="edit_phone" 
                           name="phone" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                </div>
                
                <div class="space-y-2">
                    <label for="edit_full_name" class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" 
                           id="edit_full_name" 
                           name="full_name" 
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                </div>
                
                @if($role === 'murid')
                    <div class="space-y-2">
                        <label for="edit_class" class="text-sm font-medium text-gray-700">Kelas</label>
                        <input type="text" 
                               id="edit_class" 
                               name="class" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    </div>
                @else
                    <div class="space-y-2">
                        <label for="edit_subject" class="text-sm font-medium text-gray-700">Mata Pelajaran</label>
                        <input type="text" 
                               id="edit_subject" 
                               name="subject" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                    </div>
                @endif
                
                <div class="space-y-2">
                    <label for="edit_password" class="text-sm font-medium text-gray-700">Password (biarkan kosong jika tidak diubah)</label>
                    <input type="password" 
                           id="edit_password" 
                           name="password" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                </div>
                
                <div class="flex items-center gap-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium mobile-hover">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 gradient-bg text-black py-2.5 rounded-lg hover:opacity-90 transition-opacity text-sm font-semibold mobile-hover">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-5 w-full max-w-md mx-4 glass-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-file-earmark-excel"></i>
                    Import Data {{ ucfirst($role) }} (CSV)
                </h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 mobile-hover">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form id="importForm" action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Role User</label>
                    <div id="roleDisplay" class="text-base font-semibold text-gray-900">
                        {{ ucfirst($role) }}
                    </div>
                    <input type="hidden" id="importRole" name="role" value="{{ $role }}">
                </div>
                
                <div class="space-y-2">
                    <label for="importFile" class="text-sm font-medium text-gray-700">File CSV</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <input type="file" 
                               id="importFile" 
                               name="file" 
                               accept=".csv,.txt" 
                               required
                               class="w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-gray-100 file:text-gray-700
                                      hover:file:bg-gray-200">
                        <p class="text-xs text-gray-500 mt-2">
                            Format: .csv (Comma Separated Values). Maksimal 2MB.
                        </p>
                    </div>
                </div>
                
                <div class="bg-gray-100 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        Format CSV:
                    </h4>
                    <div class="text-xs text-gray-700">
                        <code class="block bg-white p-2 rounded mb-2 border border-gray-300">
                            username,phone,password,full_name,class,subject
                        </code>
                        <ul class="space-y-1">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                <span>Baris pertama adalah header (wajib)</span>
                            </li>
                            @if($role === 'murid')
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                    <span><strong>class:</strong> Wajib untuk murid</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                    <span><strong>subject:</strong> Kosong untuk murid</span>
                                </li>
                            @else
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                    <span><strong>class:</strong> Kosong untuk guru</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                    <span><strong>subject:</strong> Wajib untuk guru</span>
                                </li>
                            @endif
                            <li class="flex items-start gap-2">
                                <i class="bi bi-circle-fill text-[6px] mt-1"></i>
                                <span>Password akan di-hash otomatis</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.download-template') }}" 
                       class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium mobile-hover">
                        <i class="bi bi-download"></i>
                        <span>Download Template</span>
                    </a>
                    
                    <button type="submit" class="flex-1 gradient-bg text-black py-2.5 rounded-lg hover:opacity-90 transition-opacity text-sm font-semibold mobile-hover">
                        <i class="bi bi-upload"></i>
                        <span>Import Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Error Import Modal -->
    @if(session()->has('import_errors'))
        <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-5 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto glass-card">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle text-red-500"></i>
                        Detail Error Import
                    </h3>
                    <button type="button" onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600 mobile-hover">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <div class="space-y-3">
                    @foreach(session('import_errors') as $error)
                        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-sm font-semibold text-red-800">Baris {{ $error['row_number'] }}</h4>
                                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Gagal</span>
                            </div>
                            
                            <div class="text-xs text-gray-700 mb-2">
                                <strong>Data:</strong>
                                @php
                                    $headers = ['username', 'phone', 'password', 'full_name', 'class', 'subject'];
                                @endphp
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($error['row'] as $index => $value)
                                        @if(isset($headers[$index]))
                                            <span class="bg-white border border-gray-300 rounded px-2 py-1">
                                                <span class="text-gray-500">{{ $headers[$index] }}:</span>
                                                <span class="font-mono">{{ $value }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="text-xs text-red-600">
                                <strong>Error:</strong>
                                <ul class="list-disc pl-4 mt-1 space-y-1">
                                    @foreach($error['errors'] as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeErrorModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium mobile-hover">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="bi bi-check-circle text-green-500 mr-3"></i>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle text-red-500 mr-3"></i>
                <div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Users Table -->
    <div class="glass-card rounded-xl p-5 border border-gray-200">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-list-ul"></i>
                Daftar {{ ucfirst($role) }} 
                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">{{ $users->count() }}</span>
            </h2>
        </div>
        
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP</th>
                            @if($role === 'murid')
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            @else
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            @endif
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-900">{{ $index + 1 }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->username }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-900">{{ $user->full_name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-900">{{ $user->phone }}</div>
                                </td>
                                @if($role === 'murid')
                                    <td class="py-3 px-4">
                                        <div class="text-sm text-gray-900">{{ $user->class ?? '-' }}</div>
                                    </td>
                                @else
                                    <td class="py-3 px-4">
                                        <div class="text-sm text-gray-900">{{ $user->subject ?? '-' }}</div>
                                    </td>
                                @endif
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                onclick="openEditModal({{ $user->id }}, '{{ $user->username }}', '{{ $user->phone }}', '{{ $user->full_name }}', '{{ $role === 'murid' ? $user->class : $user->subject }}')"
                                                class="text-sm font-medium text-gray-700 hover:text-gray-900 px-2 py-1 hover:bg-gray-100 rounded transition-colors mobile-hover">
                                            <i class="bi bi-pencil-square mr-1"></i> Edit
                                        </button>
                                        <button type="button" 
                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->full_name }}')"
                                                class="text-sm font-medium text-red-600 hover:text-red-700 px-2 py-1 hover:bg-red-50 rounded transition-colors mobile-hover">
                                            <i class="bi bi-trash mr-1"></i> Hapus
                                        </button>
                                    </div>
                                    <form id="deleteForm{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-people text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-700 font-medium mb-2">
                    Belum ada data {{ $role }}
                </h4>
                <p class="text-gray-500 text-sm mb-4">
                    Tambahkan {{ $role }} baru atau import data dari CSV
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                    <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 gradient-bg text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity mobile-hover">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah {{ ucfirst($role) }}</span>
                    </button>
                    <button type="button" onclick="openImportModal('{{ $role }}')" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors mobile-hover">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span>Import CSV</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Create Modal Functions
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('createModal').style.display = 'flex';
        document.getElementById('username').focus();
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createModal').style.display = 'none';
        document.getElementById('createForm').reset();
    }

    // Edit Modal Functions
    function openEditModal(id, username, phone, fullName, fieldValue) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_full_name').value = fullName;
        
        @if($role === 'murid')
            document.getElementById('edit_class').value = fieldValue;
        @else
            document.getElementById('edit_subject').value = fieldValue;
        @endif
        
        // PERBAIKAN: Set form action dengan benar
        document.getElementById('editForm').action = "{{ url('admin/users/edit') }}/" + id;
        
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('edit_username').focus();
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('editForm').reset();
    }

    // Import Modal Functions
    function openImportModal(role) {
        const modal = document.getElementById('importModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.getElementById('importForm').reset();
    }

    // Error Modal Functions
    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Close modals when clicking outside
    function setupModalCloseOutsideClick(modalId, closeFunction) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target.id === modalId) {
                    closeFunction();
                }
            });
        }
    }

    // Delete Confirmation
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus ${name}?`)) {
            document.getElementById(`deleteForm${id}`).submit();
        }
    }

    // Handle import form submission
    document.getElementById('importForm')?.addEventListener('submit', function(e) {
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

    // Initialize modals
    document.addEventListener('DOMContentLoaded', function() {
        setupModalCloseOutsideClick('createModal', closeCreateModal);
        setupModalCloseOutsideClick('editModal', closeEditModal);
        setupModalCloseOutsideClick('importModal', closeImportModal);
        setupModalCloseOutsideClick('errorModal', closeErrorModal);
        
        // Show error modal if exists
        if (document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'flex';
        }
    });
</script>
@endpush
@endsection