<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

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
            ->get()
            ->map(function ($guest, $index) {
                $employees = DB::table('guest_employees')
                    ->join('users', 'guest_employees.employee_id', '=', 'users.id')
                    ->where('guest_employees.guest_id', $guest->id)
                    ->pluck('users.name')
                    ->toArray();

                $guest->no = $index + 1;
                $guest->employees = implode(', ', $employees);
                $guest->photo_data_uri = $guest->photo
                    ? $this->imageToPngDataUri(public_path('uploads/' . ltrim($guest->photo, '/\\')), 160, 160)
                    : null;

                return $guest;
            });

        $pdf = PDF::loadView('admin.reports.pdf', [
            'guests' => $guests,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'letterheadImage' => $this->imageToPngDataUri(public_path('kop_laporan.jpg'), 1400, 300),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('laporan-tamu-' . $dateFrom . '-' . $dateTo . '.pdf');
    }

    /**
     * Normalisasi gambar menjadi PNG agar Dompdf tidak bergantung pada ekstensi file.
     */
    private function imageToPngDataUri($path, $maxWidth, $maxHeight)
    {
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $contents = @file_get_contents($path);
        if ($contents === false || $contents === '') {
            return null;
        }

        if (!function_exists('imagecreatefromstring')) {
            $imageInfo = @getimagesizefromstring($contents);

            return $imageInfo && isset($imageInfo['mime'])
                ? 'data:' . $imageInfo['mime'] . ';base64,' . base64_encode($contents)
                : null;
        }

        $source = @imagecreatefromstring($contents);
        if ($source === false) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, $maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        ob_start();
        $encoded = imagepng($target);
        $png = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return $encoded && $png !== false
            ? 'data:image/png;base64,' . base64_encode($png)
            : null;
    }
}
