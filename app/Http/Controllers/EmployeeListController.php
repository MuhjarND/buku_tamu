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
            'Panitera' => 3, // Sejajar dengan Sekretaris - akan digabung
            'Kepala Bagian' => 4,
            'Panitera Muda' => 5,
            'Panitera Pengganti' => 6,
            'Kepala Sub Bagian' => 7,
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
            ->orderBy('position_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Group employees by position
        $employeesByPosition = $allEmployees->groupBy('position');
        
        // Urutan kustom jabatan
        $positionOrder = $this->getPositionOrder();
        
        // Gabungkan Sekretaris & Panitera
        $mergedGroups = collect();
        $sekretarisPaniteraEmployees = collect();
        $sekretarisPaniteraDescription = '';

        foreach ($employeesByPosition as $position => $employees) {
            // Cek apakah Sekretaris atau Panitera
            if (stripos($position, 'Sekretaris') !== false || stripos($position, 'Panitera') !== false) {
                // Gabungkan ke satu group
                $sekretarisPaniteraEmployees = $sekretarisPaniteraEmployees->merge($employees);
                
                // Ambil deskripsi dari tabel positions
                if (empty($sekretarisPaniteraDescription)) {
                    $positionData = $publicPositions->firstWhere('name', $position);
                    if ($positionData && $positionData->description) {
                        $sekretarisPaniteraDescription = $positionData->description;
                    }
                }
            } else {
                // Group lainnya tetap terpisah
                $positionData = $publicPositions->firstWhere('name', $position);
                $mergedGroups->put($position, [
                    'employees' => $employees,
                    'description' => $positionData->description ?? null,
                    'order' => $this->getOrderForPosition($position, $positionOrder, $employees)
                ]);
            }
        }

        // Tambahkan group gabungan Sekretaris & Panitera jika ada
        if ($sekretarisPaniteraEmployees->isNotEmpty()) {
            $mergedGroups->put('Sekretaris & Panitera', [
                'employees' => $sekretarisPaniteraEmployees,
                'description' => $sekretarisPaniteraDescription,
                'order' => 3 // Order untuk Sekretaris & Panitera
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
        // Cari urutan dari mapping kustom
        foreach ($positionOrder as $key => $order) {
            if (stripos($position, $key) !== false || stripos($key, $position) !== false) {
                return $order;
            }
        }
        // Jika tidak ada di mapping, gunakan order dari database atau 999
        return $employees->min('position_order') ?? 999;
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
            ->orderBy('position_order', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'position', 'presence_status', 'presence_updated_at']);

        return response()->json([
            'success' => true,
            'data' => $employees,
            'updated_at' => now()->format('d M Y, H:i'),
        ]);
    }
}