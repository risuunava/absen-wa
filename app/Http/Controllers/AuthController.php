<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user_id')) {
            $role = session('user_role');
            if ($role === 'admin') {
                return redirect('/admin');
            }
            return redirect('/');
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'phone' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)
                    ->where('phone', $request->phone)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_name' => $user->username,
                'full_name' => $user->full_name
            ]);

            if ($user->role === 'admin') {
                return redirect('/admin')->with('success', 'Login berhasil sebagai admin.');
            }

            return redirect('/')->with('success', 'Login berhasil.');
        }

        return back()->with('error', 'Username, nomor HP, atau password salah.');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Logout berhasil.');
    }
}