<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\School;
use App\Models\AttendanceTimeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        if (session('user_role') !== 'admin') {
            return redirect('/')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return null;
    }

    public function dashboard()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $totalMurid = User::where('role', 'murid')->count();
        $totalGuru = User::where('role', 'guru')->count();
        
        $today = Carbon::today()->toDateString();
        $absensiHariIni = Attendance::whereDate('date', $today)->count();
        $absensiValid = Attendance::whereDate('date', $today)->where('status', 'VALID')->count();
        
        $recentAttendances = Attendance::with(['user', 'attendanceSetting'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $school = School::first();
        
        // Get active time settings
        $timeSettings = AttendanceTimeSetting::where('is_active', true)->get();
        
        // Filter settings yang aktif hari ini dengan error handling
        $todaySettings = $timeSettings->filter(function($setting) {
            try {
                return $this->isSettingActiveToday($setting);
            } catch (\Exception $e) {
                // Jika error, anggap tidak aktif
                return false;
            }
        });

        return view('admin.dashboard', compact(
            'totalMurid',
            'totalGuru',
            'absensiHariIni',
            'absensiValid',
            'recentAttendances',
            'school',
            'timeSettings',
            'todaySettings'
        ));
    }

    // Helper method untuk cek apakah setting aktif hari ini
    private function isSettingActiveToday($setting)
    {
        if (!$setting->is_active) {
            return false;
        }
        
        // Handle days_of_week yang mungkin JSON string
        $daysOfWeek = $setting->days_of_week;
        
        if (empty($daysOfWeek)) {
            return true; // Jika tidak ada setting hari, berlaku setiap hari
        }
        
        // Jika days_of_week adalah string JSON, decode dulu
        if (is_string($daysOfWeek)) {
            $decoded = json_decode($daysOfWeek, true);
            $daysArray = is_array($decoded) ? $decoded : [];
        } elseif (is_array($daysOfWeek)) {
            $daysArray = $daysOfWeek;
        } else {
            $daysArray = [];
        }
        
        if (empty($daysArray)) {
            return true;
        }
        
        $currentDay = now()->dayOfWeekIso; // 1=Senin, 7=Minggu
        return in_array($currentDay, $daysArray);
    }

    public function users(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $role = $request->query('role', 'murid');
        
        $users = User::where('role', $role)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users', 'role'));
    }

    public function createUser(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'username' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:murid,guru',
            'full_name' => 'required',
            'class' => 'required_if:role,murid',
            'subject' => 'required_if:role,guru'
        ]);

        User::create([
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'full_name' => $request->full_name,
            'class' => $request->class,
            'subject' => $request->subject
        ]);

        return back()->with('success', 'User berhasil dibuat.');
    }

    public function editUser(Request $request, $id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'full_name' => 'required',
            'class' => 'required_if:role,murid',
            'subject' => 'required_if:role,guru'
        ]);

        $user = User::findOrFail($id);
        
        $user->update([
            'username' => $request->username,
            'phone' => $request->phone,
            'full_name' => $request->full_name,
            'class' => $request->class,
            'subject' => $request->subject
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function attendance(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $role = $request->query('role', 'murid');
        $date = $request->query('date', Carbon::today()->toDateString());
        $photoVerified = $request->query('photo_verified');
        
        $attendances = Attendance::with(['user', 'attendanceSetting'])
            ->where('role', $role)
            ->whereDate('date', $date);
        
        if ($photoVerified !== null) {
            $attendances->where('photo_verified', $photoVerified);
        }
        
        $attendances = $attendances->orderBy('time', 'desc')->get();

        return view('admin.attendance', compact('attendances', 'role', 'date'));
    }

    public function verifyPhoto($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $attendance = Attendance::findOrFail($id);
        
        if (!$attendance->selfie_photo) {
            return back()->with('error', 'Absensi ini tidak memiliki foto selfie.');
        }
        
        $attendance->update([
            'photo_verified' => true,
            'note' => ($attendance->note ? $attendance->note . ' | ' : '') . 'Foto sudah diverifikasi oleh admin'
        ]);

        return back()->with('success', 'Foto selfie berhasil diverifikasi.');
    }

    public function deleteAttendance($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function updateSchool(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10'
        ]);

        $school = School::first();
        
        if ($school) {
            $school->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius
            ]);
        } else {
            School::create([
                'name' => 'Sekolah Anda',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius
            ]);
        }

        return back()->with('success', 'Lokasi sekolah berhasil diperbarui.');
    }

    public function attendanceTimeSettings(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $settings = AttendanceTimeSetting::orderBy('start_time')->get();
        
        return view('admin.time-settings', compact('settings'));
    }

    public function createTimeSetting(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:masuk,pulang,lainnya',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Validasi waktu
        if ($request->start_time >= $request->end_time && $request->end_time != '00:00') {
            return back()->with('error', 'Waktu mulai harus lebih awal dari waktu berakhir.');
        }

        // Handle days_of_week - encode sebagai JSON
        $daysOfWeek = null;
        if ($request->filled('days_of_week')) {
            $daysOfWeek = json_encode($request->days_of_week);
        }

        AttendanceTimeSetting::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'days_of_week' => $daysOfWeek,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Pengaturan waktu absen berhasil dibuat.');
    }

    public function updateTimeSetting(Request $request, $id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:masuk,pulang,lainnya',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($request->start_time >= $request->end_time && $request->end_time != '00:00') {
            return back()->with('error', 'Waktu mulai harus lebih awal dari waktu berakhir.');
        }

        $setting = AttendanceTimeSetting::findOrFail($id);
        
        // Handle days_of_week - encode sebagai JSON
        $daysOfWeek = null;
        if ($request->filled('days_of_week')) {
            $daysOfWeek = json_encode($request->days_of_week);
        }
        
        $setting->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'days_of_week' => $daysOfWeek,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Pengaturan waktu absen berhasil diperbarui.');
    }

    public function deleteTimeSetting($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $setting = AttendanceTimeSetting::findOrFail($id);
        
        // Cek apakah setting digunakan di absensi
        $usedInAttendance = Attendance::where('attendance_setting_id', $id)->exists();
        
        if ($usedInAttendance) {
            return back()->with('error', 'Pengaturan ini sudah digunakan dalam absensi. Tidak dapat dihapus.');
        }
        
        $setting->delete();

        return back()->with('success', 'Pengaturan waktu absen berhasil dihapus.');
    }

    public function getActiveTimeSettings()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $settings = AttendanceTimeSetting::where('is_active', true)->get()->map(function($setting) {
            $daysArray = $this->parseDaysOfWeek($setting->days_of_week);
            
            return [
                'id' => $setting->id,
                'name' => $setting->name,
                'start_time' => $setting->start_time->format('H:i'),
                'end_time' => $setting->end_time->format('H:i'),
                'type' => $setting->type,
                'days' => $this->getDaysDisplay($daysArray),
                'time_range' => $setting->start_time->format('H:i') . ' - ' . $setting->end_time->format('H:i'),
                'is_active_today' => $this->isSettingActiveToday($setting),
                'days_array' => $daysArray
            ];
        });

        return response()->json([
            'settings' => $settings,
            'current_time' => now()->format('H:i:s'),
            'current_day' => now()->dayOfWeekIso,
            'current_day_name' => now()->translatedFormat('l'),
            'server_time' => now()->format('Y-m-d H:i:s')
        ]);
    }

    // Helper method untuk parse days_of_week
    private function parseDaysOfWeek($daysOfWeek)
    {
        if (empty($daysOfWeek)) {
            return [];
        }
        
        if (is_array($daysOfWeek)) {
            return $daysOfWeek;
        }
        
        if (is_string($daysOfWeek)) {
            $decoded = json_decode($daysOfWeek, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    // Helper method untuk mendapatkan display days
    private function getDaysDisplay($daysArray)
    {
        if (empty($daysArray)) {
            return 'Setiap Hari';
        }

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];

        $days = array_map(function($day) use ($dayNames) {
            return $dayNames[$day] ?? $day;
        }, $daysArray);

        return implode(', ', $days);
    }

    // Debug method untuk cek waktu
    public function debugTime()
    {
        $currentTime = now()->format('H:i:s');
        $currentDay = now()->dayOfWeekIso;
        $currentDayName = now()->translatedFormat('l');
        
        $settings = AttendanceTimeSetting::where('is_active', true)->get();
        
        $activeSettings = [];
        foreach ($settings as $setting) {
            $daysArray = $this->parseDaysOfWeek($setting->days_of_week);
            $isActiveToday = in_array($currentDay, $daysArray) || empty($daysArray);
            
            // Check if current time is within range
            $start = $setting->start_time->format('H:i:s');
            $end = $setting->end_time->format('H:i:s');
            $isWithinTime = ($currentTime >= $start && $currentTime <= $end);
            
            $activeSettings[] = [
                'name' => $setting->name,
                'start' => $start,
                'end' => $end,
                'type' => $setting->type,
                'days' => $this->getDaysDisplay($daysArray),
                'days_array' => $daysArray,
                'is_active_today' => $isActiveToday,
                'is_within_time' => $isWithinTime,
                'current_time_match' => $isActiveToday && $isWithinTime ? 'YES' : 'NO'
            ];
        }

        return response()->json([
            'server_time' => now()->format('Y-m-d H:i:s'),
            'current_time' => $currentTime,
            'current_day' => $currentDay,
            'current_day_name' => $currentDayName,
            'active_settings' => $activeSettings,
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone')
        ]);
    }
}