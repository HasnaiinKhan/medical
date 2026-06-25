<?php

namespace App\Jobs;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProcessMedicineImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $medicineId,
        public string $field,
        public string $remoteUrl,
    ) {
    }

    public function handle(): void
    {
        $medicine = Medicine::find($this->medicineId);
        if (! $medicine) {
            return;
        }

        $localUrl = $this->downloadRemoteImage($this->remoteUrl);
        if (! $localUrl) {
            return;
        }

        if ($this->field === 'image_url') {
            $medicine->image_url = $localUrl;
        } elseif ($this->field === 'extra_images') {
            $existing = array_values(array_filter((array) ($medicine->extra_images ?? [])));
            $updated  = [];
            $replaced = false;

            foreach ($existing as $value) {
                if (! $replaced && (string) $value === $this->remoteUrl) {
                    $updated[] = $localUrl;
                    $replaced  = true;
                    continue;
                }

                $updated[] = $value;
            }

            if (! $replaced) {
                $updated[] = $localUrl;
            }

            $medicine->extra_images = $updated ?: null;
        }

        $medicine->save();
    }

    private function downloadRemoteImage(string $remoteUrl): ?string
    {
        if ($this->isLocalMedicineImage($remoteUrl)) {
            return $remoteUrl;
        }

        $scheme = strtolower(parse_url($remoteUrl, PHP_URL_SCHEME) ?? '');
        $host   = strtolower(parse_url($remoteUrl, PHP_URL_HOST) ?? '');

        if ($scheme !== 'https' || ! str_contains($host, '.')) {
            return null;
        }

        foreach (['localhost', '127.', '192.168.', '10.', '172.16.', '0.0.0.0', '::1'] as $blocked) {
            if (str_starts_with($host, $blocked) || $host === $blocked) {
                return null;
            }
        }

        $referer = '';
        if (str_contains($host, 'pharmeasy')) {
            $referer = 'https://pharmeasy.in/';
        } elseif (str_contains($host, 'netmeds') || str_contains($host, 'pixelbin')) {
            $referer = 'https://www.netmeds.com/';
        } elseif (str_contains($host, 'apollo') || str_contains($host, 'cloudinary')) {
            $referer = 'https://www.apollopharmacy.in/';
        } elseif (str_contains($host, '1mg') || str_contains($host, 'onemg')) {
            $referer = 'https://www.1mg.com/';
        }

        $imageData = $this->httpGetImage($remoteUrl, $referer) ?: $this->httpGetImage($remoteUrl, '');
        if (! $imageData) {
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($imageData);
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
        ];

        if (! isset($mimeToExt[$mime])) {
            Log::warning("Medicine image download skipped: unexpected MIME '{$mime}' for {$remoteUrl}");
            return null;
        }

        $dir = public_path('Images/medicines');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'med_' . Str::random(24) . '.' . $mimeToExt[$mime];
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($fullPath, $imageData) === false) {
            return null;
        }

        return asset('Images/medicines/' . $filename);
    }

    private function httpGetImage(string $url, string $referer): ?string
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
            'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language: en-IN,en;q=0.9',
        ];
        if ($referer !== '') {
            $headers[] = 'Referer: ' . $referer;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 200 && $status < 300 && is_string($body) && strlen($body) > 100) {
            return $body;
        }

        return null;
    }

    private function isLocalMedicineImage(string $url): bool
    {
        return str_contains($url, '/Images/medicines/')
            || str_contains($url, '/storage/medicines/');
    }
}
