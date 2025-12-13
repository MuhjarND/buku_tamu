<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;

class EmployeeController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Tampilkan daftar tamu untuk pegawai
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'verified');
        $search = $request->get('search');
        $employeeId = Auth::id();

        // Query tamu yang dituju oleh pegawai ini
        $query = DB::table('guests')
            ->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
            ->where('guest_employees.employee_id', $employeeId)
            ->select('guests.*')
            ->orderBy('guests.created_at', 'desc');

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('guests.status', $status);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('guests.name', 'like', "%{$search}%")
                  ->orWhere('guests.phone', 'like', "%{$search}%")
                  ->orWhere('guests.company', 'like', "%{$search}%");
            });
        }

        $guests = $query->paginate(10);

        // Hitung statistik untuk pegawai ini
        $statistics = [
            'verified' => DB::table('guests')
                ->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                ->where('guest_employees.employee_id', $employeeId)
                ->where('guests.status', 'verified')
                ->count(),
            'meeting' => DB::table('guests')
                ->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                ->where('guest_employees.employee_id', $employeeId)
                ->where('guests.status', 'meeting')
                ->count(),
            'completed' => DB::table('guests')
                ->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                ->where('guest_employees.employee_id', $employeeId)
                ->where('guests.status', 'completed')
                ->count(),
            'total' => DB::table('guests')
                ->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                ->where('guest_employees.employee_id', $employeeId)
                ->count(),
        ];

        return view('employee.guests.index', compact('guests', 'statistics', 'status', 'search'));
    }

    /**
     * Tampilkan detail tamu
     */
    public function show($id)
    {
        $employeeId = Auth::id();

        // Cek apakah tamu ini dituju oleh pegawai yang login
        $guestEmployee = DB::table('guest_employees')
            ->where('guest_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$guestEmployee) {
            return redirect()->route('employee.guests.index')
                ->with('error', 'Anda tidak memiliki akses ke tamu ini');
        }

        $ownInstruction = DB::table('guest_employees')
            ->where('guest_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        $guest = DB::table('guests')
            ->where('id', $id)
            ->first();

        if (!$guest) {
            return redirect()->route('employee.guests.index')
                ->with('error', 'Data tamu tidak ditemukan');
        }

        // Ambil daftar semua pegawai yang dituju tamu ini
        $employees = DB::table('guest_employees')
            ->join('users', 'guest_employees.employee_id', '=', 'users.id')
            ->where('guest_employees.guest_id', $id)
            ->select('users.*', 'guest_employees.is_notified', 'guest_employees.notified_at', 'guest_employees.instructions', 'guest_employees.instructions_submitted_at')
            ->get();

        // Ambil info verifikator
        $verifiedBy = null;
        if ($guest->verified_by) {
            $verifiedBy = DB::table('users')
                ->where('id', $guest->verified_by)
                ->first();
        }

        return view('employee.guests.show', compact('guest', 'employees', 'verifiedBy', 'ownInstruction'));
    }

    /**
     * Simpan arahan untuk resepsionis
     */
    public function submitInstructions(Request $request, $id)
    {
        $employeeId = Auth::id();

        // Cek akses
        $guestEmployee = DB::table('guest_employees')
            ->where('guest_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$guestEmployee) {
            return redirect()->route('employee.guests.index')
                ->with('error', 'Anda tidak memiliki akses ke tamu ini');
        }

        $request->validate([
            'instructions' => 'required|string|max:1000',
        ], [
            'instructions.required' => 'Arahan harus diisi',
        ]);

        // Simpan arahan
        DB::table('guest_employees')
            ->where('guest_id', $id)
            ->where('employee_id', $employeeId)
            ->update([
                'instructions' => $request->instructions,
                'instructions_submitted_at' => now(),
                'updated_at' => now(),
            ]);

        // Kirim notifikasi ke resepsionis
        $guest = DB::table('guests')->where('id', $id)->first();
        $receptionist = DB::table('users')
            ->where('role', 'receptionist')
            ->where('is_active', true)
            ->first();

        if ($guest && $receptionist && $receptionist->phone) {
            $this->whatsappService->sendInstructionNotification(
                $receptionist->phone,
                Auth::user()->name,
                $guest->name,
                $request->instructions,
                $guest->id
            );
        }

        return redirect()->route('employee.guest.show', $id)
            ->with('success', 'Arahan berhasil dikirim ke PTSP.');
    }
}
