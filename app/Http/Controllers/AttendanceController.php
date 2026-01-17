<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceTimeSetting;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function checkAttendanceTime(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i:s');
        $currentHourMinute = $now->format('H:i');
        $currentDay = $now->dayOfWeekIso;
        $currentDayName = $now->translatedFormat('l');
        
        $settings = AttendanceTimeSetting::where('is_active', true)->get();
        $activeSettings = [];
        
        foreach ($settings as $setting) {
            $daysArray = $setting->days_array;
            $isActiveToday = empty($daysArray) || in_array($currentDay, $daysArray);
            
            if ($isActiveToday) {
                $startTime = Carbon::parse($setting->start_time, 'Asia/Jakarta')->format('H:i:s');
                $endTime = Carbon::parse($setting->end_time, 'Asia/Jakarta')->format('H:i:s');
                $isWithinTime = ($currentTime >= $startTime && $currentTime <= $endTime);
                
                if ($isWithinTime) {
                    $activeSettings[] = [
                        'id' => $setting->id,
                        'name' => $setting->name,
                        'time_range' => Carbon::parse($startTime)->format('H:i') . ' - ' . Carbon::parse($endTime)->format('H:i'),
                        'type' => $setting->type
                    ];
                }
            }
        }
        
        $isAllowed = count($activeSettings) > 0;
        
        if ($isAllowed) {
            $message = "Absensi DIJINKAN. Jam sekarang: " . $currentHourMinute . " ($currentDayName)";
        } else {
            $message = "Absensi hanya dapat dilakukan pada jam yang telah ditentukan. ";
            $message .= "Jam sekarang: " . $currentHourMinute . " ($currentDayName)";
            
            $todaySettings = [];
            foreach ($settings as $setting) {
                $daysArray = $setting->days_array;
                $isActiveToday = empty($daysArray) || in_array($currentDay, $daysArray);
                
                if ($isActiveToday) {
                    $start = Carbon::parse($setting->start_time, 'Asia/Jakarta')->format('H:i');
                    $end = Carbon::parse($setting->end_time, 'Asia/Jakarta')->format('H:i');
                    $todaySettings[] = "{$setting->name} ($start - $end)";
                }
            }
            
            if (!empty($todaySettings)) {
                $message .= " | Waktu yang diizinkan hari ini: " . implode(', ', $todaySettings);
            }
        }
        
        return response()->json([
            'allowed' => $isAllowed,
            'message' => $message,
            'settings' => $activeSettings,
            'current_time' => $currentTime,
            'current_hour_minute' => $currentHourMinute,
            'current_day_name' => $currentDayName,
            'timezone' => 'Asia/Jakarta'
        ]);
    }
    
    public function hadir(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'required|numeric',
            'selfie_photo' => 'required|string'
        ]);
        
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i:s');
        $currentDay = $now->dayOfWeekIso;
        $today = $now->toDateString();
        $userId = session('user_id');
        $userRole = session('role', 'murid');
        
        if (!$userId) {
            return back()->with('error', 'Sesi pengguna tidak ditemukan. Silakan login ulang.');
        }
        
        $settings = AttendanceTimeSetting::where('is_active', true)->get();
        $isAllowedTime = false;
        $activeSettingId = null;
        
        foreach ($settings as $setting) {
            $daysArray = $setting->days_array;
            $isActiveToday = empty($daysArray) || in_array($currentDay, $daysArray);
            
            if ($isActiveToday) {
                $startTime = Carbon::parse($setting->start_time, 'Asia/Jakarta')->format('H:i:s');
                $endTime = Carbon::parse($setting->end_time, 'Asia/Jakarta')->format('H:i:s');
                $isWithinTime = ($currentTime >= $startTime && $currentTime <= $endTime);
                
                if ($isWithinTime) {
                    $isAllowedTime = true;
                    $activeSettingId = $setting->id;
                    break;
                }
            }
        }
        
        if (!$isAllowedTime) {
            return back()->with('error', 'Absensi hanya dapat dilakukan pada jam yang telah ditentukan.');
        }
        
        $hasAttendedToday = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->exists();
            
        if ($hasAttendedToday) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }
        
        $school = School::first();
        $radius = $school ? $school->radius : 100;
        
        if ($request->distance > $radius) {
            return back()->with('error', 'Anda berada di luar radius sekolah. Jarak: ' . round($request->distance) . 'm, maksimal: ' . $radius . 'm');
        }
        
        try {
            $selfiePath = null;
            $photoData = $request->input('selfie_photo');
            
            if ($photoData && strpos($photoData, 'data:image') === 0) {
                try {
                    list($type, $photoData) = explode(';', $photoData);
                    list(, $photoData) = explode(',', $photoData);
                    $photoData = base64_decode($photoData);
                    
                    if ($photoData === false) {
                        return back()->with('error', 'Format foto tidak valid. Silakan ambil foto ulang.');
                    }
                    
                    $fileName = 'selfie_' . $userId . '_' . time() . '_' . uniqid() . '.jpg';
                    $storagePath = 'selfies/' . $fileName;
                    
                    $saved = Storage::disk('public')->put($storagePath, $photoData);
                    
                    if (!$saved) {
                        return back()->with('error', 'Gagal menyimpan foto ke server.');
                    }
                    
                    $selfiePath = $storagePath;
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal mengunggah foto: ' . $e->getMessage());
                }
            } else {
                return back()->with('error', 'Format foto tidak valid. Silakan ambil foto ulang.');
            }
            
            $attendanceData = [
                'user_id' => $userId,
                'role' => $userRole,
                'date' => $today,
                'time' => $currentTime,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'distance' => $request->distance,
                'status' => 'hadir',
                'selfie_photo' => $selfiePath,
                'photo_verified' => false,
                'verified' => 0,
                'attendance_setting_id' => $activeSettingId,
                'attendance_type' => 'masuk',
                'created_at' => $now,
                'updated_at' => $now
            ];
            
            $attendance = Attendance::create($attendanceData);
            
            if ($attendance) {
                return back()->with('success', 'Absensi berhasil disimpan dengan foto selfie.');
            } else {
                return back()->with('error', 'Gagal menyimpan data absensi.');
            }
            
        } catch (\Exception $e) {
            try {
                $id = DB::table('attendances')->insertGetId([
                    'user_id' => $userId,
                    'role' => $userRole,
                    'date' => $today,
                    'time' => $currentTime,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'distance' => $request->distance,
                    'status' => 'hadir',
                    'selfie_photo' => $selfiePath,
                    'photo_verified' => false,
                    'verified' => 0,
                    'attendance_setting_id' => $activeSettingId,
                    'attendance_type' => 'masuk',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                if ($id) {
                    return back()->with('success', 'Absensi berhasil disimpan!');
                } else {
                    return back()->with('error', 'Gagal menyimpan data absensi (method 2).');
                }
                
            } catch (\Exception $e2) {
                return back()->with('error', 'Error 1: ' . $e->getMessage() . ' | Error 2: ' . $e2->getMessage());
            }
        }
    }
    
    public function getDistance(Request $request)
    {
        $school = School::first();
        
        if (!$school) {
            return response()->json([
                'distance' => null,
                'is_within_radius' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ]);
        }
        
        $lat1 = deg2rad($school->latitude);
        $lon1 = deg2rad($school->longitude);
        $lat2 = deg2rad($request->latitude);
        $lon2 = deg2rad($request->longitude);
        
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        
        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        $distance = 6371000 * $c;
        
        $isWithinRadius = $distance <= $school->radius;
        
        return response()->json([
            'distance' => round($distance),
            'is_within_radius' => $isWithinRadius,
            'school_radius' => $school->radius
        ]);
    }
}