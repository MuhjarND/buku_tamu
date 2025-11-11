<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeListController extends Controller
{
    /**
     * Urutan jabatan kustom
     */
    private function getPositionOrder()
    {
        return [
            'Pimpinan' => 1,
            'Hakim Tinggi' => 2,
            'Sekretaris' => 3,
            'Panitera' => 3, // Digabung dengan Sekretaris
            'Kepala Bagian' => 4,
            'Panitera Muda' => 5,
            'Kepala Sub Bagian' => 6,
            'Panitera Pengganti' => 7,
        ];
    }

    /**
     * Tampilkan daftar pegawai publik
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $positionFilter = $request->get('position');

        // Get positions yang ditampilkan di public
        $publicPositions = DB::table('positions')
            ->where('show_in_public', true)
            ->where('is_active', true)
            ->get();

        $publicPositionNames = $publicPositions->pluck('name')->toArray();

        $query = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->whereIn('position', $publicPositionNames);

        // Filter berdasarkan jabatan
        if ($positionFilter && $positionFilter !== 'all') {
            $query->where('position', $positionFilter);
        }

        // Search nama
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $allEmployees = $query
            ->orderBy('name', 'asc')
            ->get();

        // Group employees by position
        $employeesByPosition = $allEmployees->groupBy('position');
        
        // Urutan kustom jabatan
        $positionOrder = $this->getPositionOrder();
        
        // Gabungkan HANYA Sekretaris & Panitera
        $mergedGroups = collect();
        $sekretarisPaniteraEmployees = collect();
        $sekretarisPaniteraDescriptions = [];

        foreach ($employeesByPosition as $position => $employees) {
            // Deteksi Sekretaris dan Panitera (bukan Panitera Muda/Pengganti)
            $normalizedPosition = strtolower(trim($position));
            
            // Cek apakah ini Sekretaris ATAU Panitera murni (bukan turunannya)
            $isSekretaris = (stripos($normalizedPosition, 'sekretaris') !== false) && 
                           (stripos($normalizedPosition, 'sub') === false);
            
            $isPaniteraMurni = (stripos($normalizedPosition, 'panitera') !== false) && 
                              (stripos($normalizedPosition, 'muda') === false) && 
                              (stripos($normalizedPosition, 'pengganti') === false);
            
            if ($isSekretaris || $isPaniteraMurni) {
                // Gabungkan Sekretaris & Panitera
                $sekretarisPaniteraEmployees = $sekretarisPaniteraEmployees->merge($employees);
                
                // Kumpulkan deskripsi
                $positionData = $publicPositions->firstWhere('name', $position);
                if ($positionData && $positionData->description) {
                    $sekretarisPaniteraDescriptions[] = $positionData->description;
                }
            } else {
                // Group lainnya tetap terpisah
                $positionData = $publicPositions->firstWhere('name', $position);
                $order = $this->getOrderForPosition($position, $positionOrder, $employees);
                
                $mergedGroups->put($position, [
                    'employees' => $employees,
                    'description' => $positionData->description ?? null,
                    'order' => $order
                ]);
            }
        }

        // Tambahkan group gabungan Sekretaris & Panitera jika ada
        if ($sekretarisPaniteraEmployees->isNotEmpty()) {
            $mergedGroups->put('Sekretaris & Panitera', [
                'employees' => $sekretarisPaniteraEmployees,
                'description' => !empty($sekretarisPaniteraDescriptions) 
                    ? implode(' | ', array_unique($sekretarisPaniteraDescriptions)) 
                    : null,
                'order' => 3
            ]);
        }

        // Urutkan berdasarkan order
        $mergedGroups = $mergedGroups->sortBy('order');

        // Statistik (hanya yang tampil di public)
        $statistics = [
            'total' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->whereIn('position', $publicPositionNames)
                ->count(),
            'ada' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->where('presence_status', 'ada')
                ->whereIn('position', $publicPositionNames)
                ->count(),
            'keluar' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->where('presence_status', 'keluar')
                ->whereIn('position', $publicPositionNames)
                ->count(),
        ];

        // Daftar jabatan untuk filter (hanya public dengan urutan kustom)
        $positions = DB::table('positions')
            ->where('show_in_public', true)
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get(['name as position', 'order as position_order']);

        return view('public.employees', compact('mergedGroups', 'statistics', 'positions', 'search', 'positionFilter'));
    }

    /**
     * Helper untuk mendapatkan order position
     */
    private function getOrderForPosition($position, $positionOrder, $employees)
    {
        $normalizedPosition = strtolower(trim($position));
        
        // Matching dengan prioritas tinggi untuk menghindari false positive
        foreach ($positionOrder as $key => $order) {
            $normalizedKey = strtolower($key);
            
            // Exact match dulu
            if ($normalizedPosition === $normalizedKey) {
                return $order;
            }
            
            // Partial match dengan validasi
            if (stripos($normalizedPosition, $normalizedKey) !== false) {
                // Khusus untuk Panitera, pastikan bukan Panitera Muda/Pengganti
                if ($normalizedKey === 'panitera') {
                    if (stripos($normalizedPosition, 'muda') === false && 
                        stripos($normalizedPosition, 'pengganti') === false) {
                        return $order;
                    }
                } else {
                    return $order;
                }
            }
        }
        
        // Default jika tidak ketemu
        return 999;
    }

    /**
     * API untuk get status realtime (optional untuk auto-refresh)
     */
    public function getStatus()
    {
        // Get positions yang ditampilkan di public
        $publicPositions = DB::table('positions')
            ->where('show_in_public', true)
            ->where('is_active', true)
            ->pluck('name')
            ->toArray();

        $employees = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->whereIn('position', $publicPositions)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'position', 'presence_status', 'presence_updated_at']);

        return response()->json([
            'success' => true,
            'data' => $employees,
            'updated_at' => now()->format('d M Y, H:i'),
        ]);
    }
}