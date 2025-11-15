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
     * Kirim notifikasi verifikasi ke PTSP
     */
    public function sendVerificationNotification($guest, $receptionistPhone)
    {
        $verifyUrl = url("/receptionist/guest/{$guest->id}");
        
        $message = "Assalamualaikum warahmatullahi wabarakatuh,\n\n";
        $message .= "*Informasi Tamu Baru*\n\n";
        $message .= "Nama: {$guest->name}\n";
        $message .= "Telepon: {$guest->phone}\n";
        $message .= "Perusahaan: " . ($guest->company ?? '-') . "\n";
        $message .= "Keperluan: {$guest->purpose}\n\n";
        $message .= "Mohon kesediaannya untuk melakukan verifikasi melalui tautan berikut:\n{$verifyUrl}\n\n";
        $message .= "Terima kasih atas perhatian dan kerja samanya.\n";
        $message .= "Wassalamualaikum warahmatullahi wabarakatuh.\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($receptionistPhone, $message);
    }

    /**
     * Kirim notifikasi ke pegawai yang dituju
     */
    public function sendEmployeeNotification($guest, $employeePhone, $employeeName)
    {
        $verifyUrl = url("employee/guest/{$guest->id}");

        $message = "Assalamualaikum warahmatullahi wabarakatuh,\n\n";
        $message .= "*Pemberitahuan Kehadiran Tamu*\n\n";
        $message .= "Yth. {$employeeName},\n";
        $message .= "Nama Tamu  : {$guest->name}\n";
        $message .= "Telepon    : {$guest->phone}\n";
        $message .= "Perusahaan : " . ($guest->company ?? '-') . "\n";
        $message .= "Keperluan  : {$guest->purpose}\n\n";
        $message .= "Tamu telah diverifikasi dan menunggu kehadiran Anda di area PTSP.\n";
        $message .= "Silakan meninjau detail tamu melalui tautan berikut:\n{$verifyUrl}\n\n"; 
        $message .= "Wassalamualaikum warahmatullahi wabarakatuh.\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($employeePhone, $message);
    }

    /**
     * Kirim notifikasi ke tamu bahwa pegawai akan datang
     */
    public function sendGuestNotification($guestPhone, $employeeNames)
    {
        $message = "Assalamualaikum warahmatullahi wabarakatuh,\n\n";
        $message .= "*Verifikasi Kehadiran Berhasil*\n\n";
        $message .= "Terima kasih atas kesediaan Anda menunggu.\n";
        $message .= "Pegawai yang akan menemui Anda:\n";
        $message .= "- " . implode("\n- ", $employeeNames) . "\n\n";
        $message .= "Mohon berkenan menunggu di area PTSP hingga pegawai terkait tiba.\n\n";
        $message .= "Wassalamualaikum warahmatullahi wabarakatuh.\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($guestPhone, $message);
    }

    /**
     * Kirim notifikasi checkout ke tamu
     */
    public function sendCheckoutNotification($guestPhone, $guestName)
    {
        $message = "Assalamualaikum warahmatullahi wabarakatuh,\n\n";
        $message .= "*Terima Kasih Atas Kunjungan Anda*\n\n";
        $message .= "Yth. {$guestName},\n\n";
        $message .= "Terima kasih telah berkunjung ke Pengadilan Tinggi Agama Papua Barat. Checkout Anda sudah kami catat dengan baik.\n\n";
        $message .= "Sebagai bahan evaluasi pelayanan, mohon kesediaannya untuk mengisi survei berikut:\n";
        $message .= "Survey: https://s.id/surveyrokap\n\n";
        $message .= "Kami berharap dapat kembali menyambut Anda di lain kesempatan.\n";
        $message .= "Wassalamualaikum warahmatullahi wabarakatuh.\n\n";
        $message .= "*- Buku Tamu PTA Papua Barat*";

        return $this->sendMessage($guestPhone, $message);
    }
}


