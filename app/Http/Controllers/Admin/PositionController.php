<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Tampilkan daftar jabatan
     */
    public function index()
    {
        $positions = DB::table('positions')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Tampilkan form tambah jabatan
     */
    public function create()
    {
        return view('admin.positions.create');
    }

    /**
     * Simpan jabatan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:positions,name',
            'order' => 'required|integer|min:1',
            'show_in_public' => 'required|boolean',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama jabatan harus diisi',
            'name.unique' => 'Nama jabatan sudah ada',
            'order.required' => 'Urutan harus diisi',
            'order.integer' => 'Urutan harus berupa angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::table('positions')->insert([
                'name' => $request->name,
                'order' => $request->order,
                'show_in_public' => $request->show_in_public,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.positions.index')
                ->with('success', 'Jabatan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan form edit jabatan
     */
    public function edit($id)
    {
        $position = DB::table('positions')->where('id', $id)->first();

        if (!$position) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Jabatan tidak ditemukan');
        }

        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Update jabatan
     */
    public function update(Request $request, $id)
    {
        $position = DB::table('positions')->where('id', $id)->first();

        if (!$position) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Jabatan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:positions,name,' . $id,
            'order' => 'required|integer|min:1',
            'show_in_public' => 'required|boolean',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama jabatan harus diisi',
            'name.unique' => 'Nama jabatan sudah ada',
            'order.required' => 'Urutan harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::table('positions')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'order' => $request->order,
                    'show_in_public' => $request->show_in_public,
                    'description' => $request->description,
                    'is_active' => $request->is_active,
                    'updated_at' => now(),
                ]);

            return redirect()->route('admin.positions.index')
                ->with('success', 'Jabatan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus jabatan
     */
    public function destroy($id)
    {
        try {
            $position = DB::table('positions')->where('id', $id)->first();

            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan tidak ditemukan',
                ], 404);
            }

            // Cek apakah ada user dengan jabatan ini
            $userCount = DB::table('users')
                ->where('position', $position->name)
                ->count();

            if ($userCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak bisa hapus. Ada {$userCount} pegawai dengan jabatan ini.",
                ], 400);
            }

            DB::table('positions')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}