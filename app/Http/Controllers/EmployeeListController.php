<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeListController extends Controller
{
    /**
     * Tampilkan daftar pegawai publik
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $positionFilter = $request->get('position');

        $query = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true);

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
        
        // Urutkan groups berdasarkan position_order terendah dalam setiap group
        $employeesByPosition = $employeesByPosition->sortBy(function($employees, $position) {
            return $employees->min('position_order');
        });

        // Statistik
        $statistics = [
            'total' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->count(),
            'ada' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->where('presence_status', 'ada')
                ->count(),
            'keluar' => DB::table('users')
                ->where('role', 'employee')
                ->where('is_active', true)
                ->where('presence_status', 'keluar')
                ->count(),
        ];

        // Daftar jabatan untuk filter
        $positions = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->whereNotNull('position')
            ->groupBy('position', 'position_order')
            ->orderBy('position_order', 'asc')
            ->get(['position', 'position_order']);

        return view('public.employees', compact('employeesByPosition', 'statistics', 'positions', 'search', 'positionFilter'));
    }

    /**
     * API untuk get status realtime (optional untuk auto-refresh)
     */
    public function getStatus()
    {
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('position_order', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'position', 'presence_status', 'presence_updated_at']);

        return response()->json([
            'success' => true,
            'data' => $employees,
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}