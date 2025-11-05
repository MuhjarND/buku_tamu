<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan
     */
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', date('Y-m-01'));
        $dateTo = $request->get('date_to', date('Y-m-d'));
        $status = $request->get('status', 'all');
        $employeeId = $request->get('employee_id', 'all');

        // Query dasar
        $query = DB::table('guests')
            ->whereDate('check_in_time', '>=', $dateFrom)
            ->whereDate('check_in_time', '<=', $dateTo);

        // Filter status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter pegawai
        if ($employeeId !== 'all') {
            $query->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                  ->where('guest_employees.employee_id', $employeeId);
        }

        $guests = $query->select('guests.*')
            ->orderBy('guests.check_in_time', 'desc')
            ->get();

        // Statistik periode
        $statistics = [
            'total_visitors' => $guests->count(),
            'completed' => $guests->where('status', 'completed')->count(),
            'pending' => $guests->where('status', 'pending')->count(),
            'verified' => $guests->where('status', 'verified')->count(),
            'meeting' => $guests->where('status', 'meeting')->count(),
        ];

        // Statistik harian
        $dailyStats = DB::table('guests')
            ->select(DB::raw('DATE(check_in_time) as date'), DB::raw('COUNT(*) as count'))
            ->whereDate('check_in_time', '>=', $dateFrom)
            ->whereDate('check_in_time', '<=', $dateTo)
            ->groupBy(DB::raw('DATE(check_in_time)'))
            ->orderBy('date', 'asc')
            ->get();

        // Top pegawai (yang paling banyak dikunjungi)
        $topEmployees = DB::table('guest_employees')
            ->join('users', 'guest_employees.employee_id', '=', 'users.id')
            ->join('guests', 'guest_employees.guest_id', '=', 'guests.id')
            ->whereDate('guests.check_in_time', '>=', $dateFrom)
            ->whereDate('guests.check_in_time', '<=', $dateTo)
            ->select('users.name', DB::raw('COUNT(*) as total_guests'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_guests', 'desc')
            ->limit(5)
            ->get();

        // Daftar pegawai untuk filter
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.reports.index', compact(
            'guests', 
            'statistics', 
            'dailyStats', 
            'topEmployees',
            'employees',
            'dateFrom', 
            'dateTo', 
            'status',
            'employeeId'
        ));
    }

    /**
     * Export laporan ke Excel
     */
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from', date('Y-m-01'));
        $dateTo = $request->get('date_to', date('Y-m-d'));
        $status = $request->get('status', 'all');
        $employeeId = $request->get('employee_id', 'all');

        // Query data
        $query = DB::table('guests')
            ->whereDate('check_in_time', '>=', $dateFrom)
            ->whereDate('check_in_time', '<=', $dateTo);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($employeeId !== 'all') {
            $query->join('guest_employees', 'guests.id', '=', 'guest_employees.guest_id')
                  ->where('guest_employees.employee_id', $employeeId);
        }

        $guests = $query->select('guests.*')
            ->orderBy('guests.check_in_time', 'desc')
            ->get();

        // Set header untuk download CSV
        $filename = 'laporan-tamu-' . $dateFrom . '-' . $dateTo . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // BOM untuk Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header CSV
        fputcsv($output, [
            'No',
            'Nama',
            'Telepon',
            'Email',
            'Perusahaan',
            'Keperluan',
            'Status',
            'Check In',
            'Check Out',
            'Pegawai Dituju'
        ]);

        // Data rows
        foreach ($guests as $index => $guest) {
            // Ambil nama pegawai yang dituju
            $employees = DB::table('guest_employees')
                ->join('users', 'guest_employees.employee_id', '=', 'users.id')
                ->where('guest_employees.guest_id', $guest->id)
                ->pluck('users.name')
                ->toArray();

            $statusLabel = [
                'pending' => 'Menunggu',
                'verified' => 'Terverifikasi',
                'meeting' => 'Bertemu',
                'completed' => 'Selesai',
            ];

            fputcsv($output, [
                $index + 1,
                $guest->name,
                $guest->phone,
                $guest->email ?? '-',
                $guest->company ?? '-',
                $guest->purpose,
                $statusLabel[$guest->status] ?? $guest->status,
                $guest->check_in_time ? date('d/m/Y H:i', strtotime($guest->check_in_time)) : '-',
                $guest->check_out_time ? date('d/m/Y H:i', strtotime($guest->check_out_time)) : '-',
                implode(', ', $employees)
            ]);
        }

        fclose($output);
        exit;
    }
}