<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\School;

class AttendanceController extends Controller
{
    /**
     * Form absensi berdasarkan nomor HP
     */
    public function form($phone)
    {
        $student = Student::where('phone', $phone)->firstOrFail();
        return view('hadir', compact('student'));
    }

    /**
     * Simpan absensi
     */
    public function store(Request $request)
    {
        // VALIDASI WAJIB
        if (!$request->latitude || !$request->longitude) {
            return response('Lokasi tidak terdeteksi. Aktifkan GPS.', 400);
        }

        $school = School::first();
        if (!$school) {
            return response('Data sekolah belum diatur.', 500);
        }

        // Hitung jarak dari sekolah (meter)
        $distance = $this->calculateDistance(
            $school->latitude,
            $school->longitude,
            $request->latitude,
            $request->longitude
        );

        // Status absensi
        $status = $distance <= $school->radius ? 'VALID' : 'INVALID';

        // Simpan ke database
        Attendance::create([
            'student_id' => $request->student_id,
            'date'       => now()->toDateString(),
            'time'       => now()->toTimeString(),
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'distance'   => round($distance, 2),
            'status'     => $status,
            'note'       => $status === 'VALID'
                ? 'Hadir'
                : 'Di luar area sekolah'
        ]);

        return response("Absensi $status", 200);
    }

    /**
     * Hitung jarak dua koordinat (meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
