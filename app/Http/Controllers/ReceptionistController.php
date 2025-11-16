<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;

class ReceptionistController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Tampilkan daftar tamu
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = DB::table('guests')
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

        // Hitung statistik
        $statistics = [
            'pending' => DB::table('guests')->where('status', 'pending')->count(),
            'verified' => DB::table('guests')->where('status', 'verified')->count(),
            'meeting' => DB::table('guests')->where('status', 'meeting')->count(),
            'completed' => DB::table('guests')->where('status', 'completed')->count(),
            'total' => DB::table('guests')->count(),
        ];

        return view('receptionist.guests.index', compact('guests', 'statistics', 'status', 'search'));
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
            return redirect()->route('receptionist.guests.index')
                ->with('error', 'Data tamu tidak ditemukan');
        }

        // Ambil daftar pegawai yang dituju
        $employees = DB::table('guest_employees')
            ->join('users', 'guest_employees.employee_id', '=', 'users.id')
            ->where('guest_employees.guest_id', $id)
            ->select('users.*', 'guest_employees.is_notified', 'guest_employees.notified_at')
            ->get();

        // Ambil info verifikator jika ada
        $verifiedBy = null;
        if ($guest->verified_by) {
            $verifiedBy = DB::table('users')
                ->where('id', $guest->verified_by)
                ->first();
        }

        return view('receptionist.guests.show', compact('guest', 'employees', 'verifiedBy'));
    }

    /**
     * Verifikasi tamu
     */
    public function verify($id)
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

            if ($guest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah diverifikasi',
                ], 400);
            }

            // Update status tamu
            DB::table('guests')
                ->where('id', $id)
                ->update([
                    'status' => 'verified',
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'updated_at' => now(),
                ]);

            // Ambil daftar pegawai yang dituju
            $employees = DB::table('guest_employees')
                ->join('users', 'guest_employees.employee_id', '=', 'users.id')
                ->where('guest_employees.guest_id', $id)
                ->select('users.id', 'users.name', 'users.phone')
                ->get();

            $employeeNames = [];

            // Kirim notifikasi ke setiap pegawai
            foreach ($employees as $employee) {
                if ($employee->phone) {
                    $this->whatsappService->sendEmployeeNotification($guest, $employee->phone, $employee->name);
                    
                    // Update status notifikasi
                    DB::table('guest_employees')
                        ->where('guest_id', $id)
                        ->where('employee_id', $employee->id)
                        ->update([
                            'is_notified' => true,
                            'notified_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
                
                $employeeNames[] = $employee->name;
            }

            // Kirim notifikasi ke tamu
            if ($guest->phone) {
                $this->whatsappService->sendGuestNotification($guest->phone, $employeeNames);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tamu berhasil diverifikasi. Notifikasi telah dikirim ke pegawai dan tamu.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update status tamu menjadi sedang bertemu
     */
    public function startMeeting($id)
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

            if ($guest->status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tamu belum siap untuk pertemuan',
                ], 400);
            }

            DB::table('guests')
                ->where('id', $id)
                ->update([
                    'status' => 'meeting',
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status tamu diperbarui menjadi sedang bertemu.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Checkout tamu setelah pertemuan selesai
     */
    public function checkout($id)
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

            if ($guest->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah checkout',
                ], 400);
            }

            if ($guest->status !== 'meeting') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tamu belum dalam pertemuan',
                ], 400);
            }

            DB::table('guests')
                ->where('id', $id)
                ->update([
                    'status' => 'completed',
                    'check_out_time' => now(),
                    'updated_at' => now(),
                ]);

            if ($guest->phone) {
                $this->whatsappService->sendCheckoutNotification($guest->phone, $guest->name);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout tamu berhasil dicatat.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan halaman status kehadiran pegawai
     */
    public function presenceStatus()
    {
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('position_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('receptionist.presence-status', compact('employees'));
    }

    /**
     * Update status kehadiran pegawai
     */
    public function updatePresenceStatus(Request $request, $id)
    {
        try {
            $status = $request->input('status');

            if (!in_array($status, ['ada', 'keluar'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid',
                ], 400);
            }

            DB::table('users')
                ->where('id', $id)
                ->where('role', 'employee')
                ->update([
                    'presence_status' => $status,
                    'presence_updated_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tolak tamu
     */
    public function reject($id, Request $request)
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

            if ($guest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah diproses',
                ], 400);
            }

            $reason = $request->input('reason', 'Data tidak valid');

            // Hapus data tamu dan relasinya
            DB::table('guest_employees')->where('guest_id', $id)->delete();
            DB::table('guests')->where('id', $id)->delete();

            // Kirim notifikasi penolakan ke tamu
            if ($guest->phone) {
                $message = "Assalamualaikum warahmatullahi wabarakatuh,\n\n";
                $message .= "*Verifikasi Ditolak*\n\n";
                $message .= "Yth. {$guest->name},\n\n";
                $message .= "Mohon maaf, data Anda belum dapat kami verifikasi.\n";
                $message .= "Alasan: {$reason}\n\n";
                $message .= "Silakan melakukan pendaftaran kembali dengan data yang telah diperbaiki.\n\n";
                $message .= "Wassalamualaikum warahmatullahi wabarakatuh.\n\n";
                $message .= "*- Buku Tamu PTA Papua Barat*";
                
                $this->whatsappService->sendMessage($guest->phone, $message);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tamu berhasil ditolak dan notifikasi telah dikirim.',
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
