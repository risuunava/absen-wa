<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\School;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    public function hadir(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole === 'admin') {
            return redirect('/admin')->with('error', 'Admin tidak dapat melakukan absensi.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'required|numeric',
            'selfie_photo' => 'required' // Hanya validasi required, tidak pakai image validation dulu
        ]);

        $school = School::first();
        if (!$school) {
            return back()->with('error', 'Konfigurasi sekolah belum ditentukan.');
        }

        $distance = $request->distance;
        $status = $distance <= $school->radius ? 'VALID' : 'INVALID';

        $today = Carbon::today()->toDateString();
        
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        // Upload foto selfie dari base64
        $selfiePath = null;
        $photoData = $request->input('selfie_photo');
        
        if ($photoData && strpos($photoData, 'data:image') === 0) {
            // Data adalah base64
            try {
                // Decode base64 image
                list($type, $photoData) = explode(';', $photoData);
                list(, $photoData) = explode(',', $photoData);
                $photoData = base64_decode($photoData);
                
                // Generate filename
                $fileName = 'selfie_' . $userId . '_' . time() . '.jpg';
                $storagePath = 'selfies/' . $fileName;
                
                // Simpan ke storage
                Storage::disk('public')->put($storagePath, $photoData);
                
                $selfiePath = $storagePath;
            } catch (\Exception $e) {
                // Jika error saat upload foto, tetap lanjutkan absensi tanpa foto
                $selfiePath = null;
            }
        }

        // Simpan data absensi
        $attendance = Attendance::create([
            'user_id' => $userId,
            'role' => $userRole,
            'date' => $today,
            'time' => Carbon::now()->toTimeString(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance' => $distance,
            'status' => $status,
            'note' => $status === 'VALID' ? 'Absensi valid' : 'Di luar radius sekolah',
            'selfie_photo' => $selfiePath,
            'photo_verified' => false
        ]);

        if ($attendance) {
            return back()->with('success', 'Absensi berhasil disimpan dengan foto selfie. Status: ' . $status);
        } else {
            return back()->with('error', 'Gagal menyimpan absensi. Silakan coba lagi.');
        }
    }

    public function getDistance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $school = School::first();
        if (!$school) {
            return response()->json(['error' => 'Sekolah tidak ditemukan'], 404);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $school->latitude,
            $school->longitude
        );

        return response()->json([
            'distance' => round($distance, 2),
            'radius' => $school->radius,
            'school_name' => $school->name,
            'is_within_radius' => $distance <= $school->radius
        ]);
    }
}