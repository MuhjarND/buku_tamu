<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = config('fonnte.token');
        $this->apiUrl = config('fonnte.api_url');
    }

    /**
     * Kirim pesan WhatsApp
     *
     * @param string $phone Nomor telepon tujuan (format: 628xxx)
     * @param string $message Isi pesan
     * @return array
     */
    public function sendMessage($phone, $message)
    {
        try {
            // Format nomor telepon (pastikan format 628xxx)
            $phone = $this->formatPhone($phone);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();

            Log::info('WhatsApp notification sent', [
                'phone' => $phone,
                'status' => $response->successful(),
                'response' => $result,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format nomor telepon ke format 628xxx
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhone($phone)
    {
        // Hapus karakter selain angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak diawali 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Kirim notifikasi verifikasi ke resepsionis
     */
    public function sendVerificationNotification($guest, $receptionistPhone)
    {
        $verifyUrl = url("/receptionist/guest/{$guest->id}");
        
        $message = "*Tamu Baru Memerlukan Verifikasi*\n\n";
        $message .= "Nama: {$guest->name}\n";
        $message .= "Telepon: {$guest->phone}\n";
        $message .= "Perusahaan: " . ($guest->company ?? '-') . "\n";
        $message .= "Keperluan: {$guest->purpose}\n\n";
        $message .= "Silakan verifikasi tamu:\n{$verifyUrl}\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($receptionistPhone, $message);
    }

    /**
     * Kirim notifikasi ke pegawai yang dituju
     */
    public function sendEmployeeNotification($guest, $employeePhone, $employeeName)
    {
        $verifyUrl = url("employee/guest/{$guest->id}");

        $message = "*Anda Memiliki Tamu*\n\n";
        $message .= "Nama Tamu: {$guest->name}\n";
        $message .= "Telepon: {$guest->phone}\n";
        $message .= "Perusahaan: " . ($guest->company ?? '-') . "\n";
        $message .= "Keperluan: {$guest->purpose}\n";
        $message .= "*Tamu telah diverifikasi dan menunggu Anda.*\n";
        $message .= "Tautan untuk melihat. \n{$verifyUrl}\n\n"; 
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($employeePhone, $message);
    }

    /**
     * Kirim notifikasi ke tamu bahwa pegawai akan datang
     */
    public function sendGuestNotification($guestPhone, $employeeNames)
    {
        $message = "*Verifikasi Berhasil*\n\n";
        $message .= "Terima kasih telah menunggu.\n";
        $message .= "Pegawai yang Anda tuju akan segera menemui Anda:\n";
        $message .= "- " . implode("\n- ", $employeeNames) . "\n\n";
        $message .= "Mohon menunggu di area resepsionis.\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($guestPhone, $message);
    }

    /**
     * Kirim notifikasi checkout ke tamu
     */
    public function sendCheckoutNotification($guestPhone, $guestName)
    {
        $message = "*Terima Kasih Atas Kunjungan Anda*\n\n";
        $message .= "Halo {$guestName},\n\n";
        $message .= "Terima kasih telah berkunjung. Checkout Anda telah berhasil dicatat.\n\n";
        $message .= "Kami sangat menghargai kunjungan Anda. Mohon bantu kami untuk meningkatkan pelayanan dengan mengisi survey kepuasan:\n\n";
        $message .= "🔗 Survey: s.id/surveyrokap\n\n";
        $message .= "Sampai jumpa lagi!\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($guestPhone, $message);
    }
}