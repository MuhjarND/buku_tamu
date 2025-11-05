<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    /**
     * Tampilkan daftar semua tamu
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = DB::table('guests')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan tanggal
        if ($dateFrom) {
            $query->whereDate('check_in_time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('check_in_time', '<=', $dateTo);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $guests = $query->paginate(15);

        // Hitung statistik
        $statistics = [
            'today' => DB::table('guests')->whereDate('check_in_time', today())->count(),
            'pending' => DB::table('guests')->where('status', 'pending')->count(),
            'verified' => DB::table('guests')->where('status', 'verified')->count(),
            'meeting' => DB::table('guests')->where('status', 'meeting')->count(),
            'completed' => DB::table('guests')->where('status', 'completed')->count(),
            'total' => DB::table('guests')->count(),
        ];

        return view('admin.guests.index', compact('guests', 'statistics', 'status', 'search', 'dateFrom', 'dateTo'));
    }

    /**
     * Tampilkan detail tamu
     */
    public function show($id)
    {
        $guest = DB::table('guests')
            ->where('id', $id)
            ->first();

        if (!$guest) {
            return redirect()->route('admin.guests.index')
                ->with('error', 'Data tamu tidak ditemukan');
        }

        // Ambil daftar pegawai yang dituju
        $employees = DB::table('guest_employees')
            ->join('users', 'guest_employees.employee_id', '=', 'users.id')
            ->where('guest_employees.guest_id', $id)
            ->select('users.*', 'guest_employees.is_notified', 'guest_employees.notified_at')
            ->get();

        // Ambil info verifikator
        $verifiedBy = null;
        if ($guest->verified_by) {
            $verifiedBy = DB::table('users')
                ->where('id', $guest->verified_by)
                ->first();
        }

        return view('admin.guests.show', compact('guest', 'employees', 'verifiedBy'));
    }

    /**
     * Hapus tamu
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $guest = DB::table('guests')->where('id', $id)->first();

            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tamu tidak ditemukan',
                ], 404);
            }

            // Hapus foto jika ada
            if ($guest->photo) {
                Storage::disk('public')->delete($guest->photo);
            }

            // Hapus relasi guest_employees
            DB::table('guest_employees')->where('guest_id', $id)->delete();

            // Hapus guest
            DB::table('guests')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil dihapus',
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