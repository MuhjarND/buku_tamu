<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user
     */
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');
        $search = $request->get('search');

        $query = DB::table('users')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan role
        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        // Hitung statistik
        $statistics = [
            'admin' => DB::table('users')->where('role', 'admin')->count(),
            'receptionist' => DB::table('users')->where('role', 'receptionist')->count(),
            'employee' => DB::table('users')->where('role', 'employee')->count(),
            'active' => DB::table('users')->where('is_active', true)->count(),
            'inactive' => DB::table('users')->where('is_active', false)->count(),
            'total' => DB::table('users')->count(),
        ];

        return view('admin.users.index', compact('users', 'statistics', 'role', 'search'));
    }

    /**
     * Tampilkan form tambah user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,receptionist,employee',
            'position' => 'nullable|string|max:255',
            'position_order' => 'nullable|integer',
            'keterangan' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Nomor telepon harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Upload foto jika ada
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('user_photos', $photoName, 'public');
            }

            DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'photo' => $photoPath,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'position' => $request->position,
                'position_order' => $request->position_order ?? 999,
                'keterangan' => $request->keterangan,
                'presence_status' => 'ada',
                'is_active' => $request->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus foto jika ada error
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan form edit user
     */
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,receptionist,employee',
            'position' => 'nullable|string|max:255',
            'position_order' => 'nullable|integer',
            'keterangan' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Nomor telepon harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'position' => $request->position,
                'position_order' => $request->position_order ?? 999,
                'keterangan' => $request->keterangan,
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ];

            // Upload foto baru jika ada
            if ($request->hasFile('photo')) {
                // Hapus foto lama
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('user_photos', $photoName, 'public');
                $updateData['photo'] = $photoPath;
            }

            // Update password jika diisi
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            DB::table('users')
                ->where('id', $id)
                ->update($updateData);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            // Tidak bisa hapus diri sendiri
            if ($id == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa menghapus akun sendiri',
                ], 400);
            }

            // Hapus foto jika ada
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Hapus relasi guest_employees jika user adalah employee
            if ($user->role === 'employee') {
                DB::table('guest_employees')->where('employee_id', $id)->delete();
            }

            // Hapus user
            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}