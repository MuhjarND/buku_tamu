<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek user di database menggunakan Query Builder
        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Email tidak terdaftar')
                ->withInput();
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->with('error', 'Password salah')
                ->withInput();
        }

        // Cek apakah user aktif
        if (!$user->is_active) {
            return redirect()->back()
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.')
                ->withInput();
        }

        // Login user dengan Auth::loginUsingId
        Auth::loginUsingId($user->id, $request->has('remember'));

        // Redirect berdasarkan role
        return $this->redirectBasedOnRole($user->role);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }

    /**
     * Redirect berdasarkan role
     */
    protected function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.guests.index')
                    ->with('success', 'Selamat datang, Admin!');
            
            case 'receptionist':
                return redirect()->route('receptionist.guests.index')
                    ->with('success', 'Selamat datang, PTSP!');
            
            case 'employee':
                return redirect()->route('employee.guests.index')
                    ->with('success', 'Selamat datang!');
            
            default:
                return redirect()->route('dashboard')
                    ->with('success', 'Selamat datang!');
        }
    }
}
