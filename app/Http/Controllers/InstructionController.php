<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;

class InstructionController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function show($token)
    {
        $guestEmployee = DB::table('guest_employees')
            ->join('guests', 'guest_employees.guest_id', '=', 'guests.id')
            ->join('users', 'guest_employees.employee_id', '=', 'users.id')
            ->where('guest_employees.instruction_token', $token)
            ->select(
                'guest_employees.*',
                'guests.name as guest_name',
                'guests.id as guest_id',
                'guests.company as guest_company',
                'guests.purpose as guest_purpose',
                'users.name as employee_name'
            )
            ->first();

        if (!$guestEmployee) {
            abort(404, 'Tautan arahan tidak valid.');
        }

        return view('public.instructions', [
            'token' => $token,
            'guestEmployee' => $guestEmployee,
        ]);
    }

    public function submit(Request $request, $token)
    {
        $guestEmployee = DB::table('guest_employees')
            ->where('instruction_token', $token)
            ->first();

        if (!$guestEmployee) {
            abort(404, 'Tautan arahan tidak valid.');
        }

        $request->validate([
            'instructions' => 'required|string|max:1000',
        ], [
            'instructions.required' => 'Arahan harus diisi',
        ]);

        DB::table('guest_employees')
            ->where('instruction_token', $token)
            ->update([
                'instructions' => $request->instructions,
                'instructions_submitted_at' => now(),
                'updated_at' => now(),
            ]);

        // Notifikasi ke resepsionis
        $guest = DB::table('guests')->where('id', $guestEmployee->guest_id)->first();
        $employee = DB::table('users')->where('id', $guestEmployee->employee_id)->first();
        $receptionist = DB::table('users')
            ->where('role', 'receptionist')
            ->where('is_active', true)
            ->first();

        if ($guest && $employee && $receptionist && $receptionist->phone) {
            $this->whatsappService->sendInstructionNotification(
                $receptionist->phone,
                $employee->name,
                $guest->name,
                $request->instructions,
                $guest->id
            );
        }

        return redirect()->back()->with('success', 'Arahan berhasil dikirim ke PTSP.');
    }
}
