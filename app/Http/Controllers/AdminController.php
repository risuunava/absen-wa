<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Helper method untuk proteksi manual
    private function checkAdmin()
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        if (session('user_role') !== 'admin') {
            return redirect('/')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return null; // Return null jika user adalah admin
    }

    public function dashboard()
    {
        // Proteksi manual
        $check = $this->checkAdmin();
        if ($check) return $check;

        $totalMurid = User::where('role', 'murid')->count();
        $totalGuru = User::where('role', 'guru')->count();
        
        $today = Carbon::today()->toDateString();
        $absensiHariIni = Attendance::whereDate('date', $today)->count();
        $absensiValid = Attendance::whereDate('date', $today)->where('status', 'VALID')->count();
        
        $recentAttendances = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $school = School::first();

        return view('admin.dashboard', compact(
            'totalMurid',
            'totalGuru',
            'absensiHariIni',
            'absensiValid',
            'recentAttendances',
            'school'
        ));
    }

    public function users(Request $request)
    {
        // Proteksi manual
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
        // Proteksi manual
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
        // Proteksi manual
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
        // Proteksi manual
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
        // Proteksi manual
        $check = $this->checkAdmin();
        if ($check) return $check;

        $role = $request->query('role', 'murid');
        $date = $request->query('date', Carbon::today()->toDateString());
        
        $attendances = Attendance::with('user')
            ->where('role', $role)
            ->whereDate('date', $date)
            ->orderBy('time', 'desc')
            ->get();

        return view('admin.attendance', compact('attendances', 'role', 'date'));
    }

   public function updateSchool(Request $request)
{
    // Proteksi manual
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
}