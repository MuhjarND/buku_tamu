<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\WhatsAppService;

class GuestController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Tampilkan form registrasi tamu
     */
    public function index()
    {
        // Ambil daftar pegawai yang aktif dengan status kehadiran
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('position_order', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'position', 'presence_status']);

        return view('guest.register', compact('employees'));
    }

    /**
     * Simpan data tamu baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'purpose' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:users,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'purpose.required' => 'Keperluan harus diisi',
            'employee_ids.required' => 'Pilih minimal satu pegawai',
            'employee_ids.min' => 'Pilih minimal satu pegawai',
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
                $photoPath = $photo->storeAs('guest_photos', $photoName, 'public');
            }

            // Simpan data tamu
            $guestId = DB::table('guests')->insertGetId([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'company' => $request->company,
                'purpose' => $request->purpose,
                'photo' => $photoPath,
                'status' => 'pending',
                'check_in_time' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan relasi dengan pegawai
            $employeeData = [];
            foreach ($request->employee_ids as $employeeId) {
                $employeeData[] = [
                    'guest_id' => $guestId,
                    'employee_id' => $employeeId,
                    'is_notified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('guest_employees')->insert($employeeData);

            // Ambil data tamu yang baru disimpan
            $guest = DB::table('guests')->where('id', $guestId)->first();

            // Kirim notifikasi ke resepsionis
            $receptionist = DB::table('users')
                ->where('role', 'receptionist')
                ->where('is_active', true)
                ->first();

            if ($receptionist && $receptionist->phone) {
                $this->whatsappService->sendVerificationNotification($guest, $receptionist->phone);
            }

            DB::commit();

            return redirect()->route('guest.success')
                ->with('success', 'Data Anda telah berhasil disimpan. Mohon menunggu verifikasi dari resepsionis.');

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
     * Halaman sukses setelah registrasi
     */
    public function success()
    {
        return view('guest.success');
    }

    /**
     * Halaman checkout untuk tamu
     */
    public function checkoutPage()
    {
        return view('guest.checkout');
    }

    /**
     * Checkout berdasarkan nomor telepon
     */
    public function checkoutByPhone(Request $request)
    {
        $phone = $request->input('phone');

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon harus diisi',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Cari tamu berdasarkan nomor telepon yang belum checkout
            $guest = DB::table('guests')
                ->where('phone', $phone)
                ->whereIn('status', ['verified', 'meeting'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telepon tidak ditemukan atau Anda sudah checkout',
                ], 404);
            }

            // Update status dan waktu checkout
            DB::table('guests')
                ->where('id', $guest->id)
                ->update([
                    'status' => 'completed',
                    'check_out_time' => now(),
                    'updated_at' => now(),
                ]);

            // Kirim notifikasi checkout
            $this->whatsappService->sendCheckoutNotification($guest->phone, $guest->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil. Terima kasih atas kunjungan Anda.',
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
     * API untuk checkout tamu
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

            // Update status dan waktu checkout
            DB::table('guests')
                ->where('id', $id)
                ->update([
                    'status' => 'completed',
                    'check_out_time' => now(),
                    'updated_at' => now(),
                ]);

            // Kirim notifikasi checkout
            $this->whatsappService->sendCheckoutNotification($guest->phone, $guest->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil. Terima kasih atas kunjungan Anda.',
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