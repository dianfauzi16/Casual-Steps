<?php

namespace App\Core;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

class CloudinaryHelper {
    private static $isConfigured = false;

    private static function configure() {
        if (self::$isConfigured) {
            return true;
        }

        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        $cloudinaryUrl = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? ($_SERVER['CLOUDINARY_URL'] ?? null));
        
        if (empty($cloudinaryUrl)) {
            $envPath = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envPath)) {
                $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($envLines as $line) {
                    if (strpos(trim($line), 'CLOUDINARY_URL=') === 0) {
                        $cloudinaryUrl = trim(str_replace('"', '', substr(trim($line), 15)));
                        break;
                    }
                }
            }
        }

        if ($cloudinaryUrl) {
            Configuration::instance($cloudinaryUrl);
            self::$isConfigured = true;
            return true;
        }

        return false;
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param string $filePath The temporary path of the uploaded file
     * @param string $folder The folder in Cloudinary to upload to
     * @return string|false Returns the secure URL if successful, false otherwise
     * @throws Exception If upload fails
     */
    public static function upload($filePath, $folder = 'casual_steps_products') {
        if (!self::configure()) {
            throw new Exception("CLOUDINARY_URL belum dikonfigurasi.");
        }

        $uploadApi = new UploadApi();
        $uploadResult = $uploadApi->upload($filePath, [
            'folder' => $folder
        ]);

        if (isset($uploadResult['secure_url'])) {
            return $uploadResult['secure_url'];
        }

        return false;
    }

    /**
     * Mengoptimasi URL gambar Cloudinary dengan parameter transformasi otomatis.
     *
     * Cara kerja:
     * - q_auto  → Cloudinary otomatis memilih kualitas terbaik (biasanya 70-80%)
     *             tanpa penurunan kualitas visual yang terlihat.
     * - f_auto  → Cloudinary otomatis mengubah format ke WebP, AVIF, dll.
     *             sesuai dukungan browser (sangat hemat bandwidth).
     *
     * Contoh:
     * Input  : https://res.cloudinary.com/demo/image/upload/sample.jpg
     * Output : https://res.cloudinary.com/demo/image/upload/q_auto,f_auto/sample.jpg
     *
     * @param string|null $url URL gambar Cloudinary asli
     * @return string URL yang sudah dioptimasi, atau string kosong jika URL null/kosong
     */
    public static function optimizeUrl(?string $url): string {
        // Jika URL kosong atau bukan URL Cloudinary, kembalikan apa adanya
        if (empty($url)) {
            return '';
        }

        // Periksa apakah URL ini memang URL Cloudinary
        if (strpos($url, 'res.cloudinary.com') === false) {
            return $url;
        }

        // Pastikan parameter transformasi belum ada agar tidak duplikat
        if (strpos($url, 'q_auto') !== false || strpos($url, 'f_auto') !== false) {
            return $url;
        }

        // Sisipkan parameter 'q_auto,f_auto' setelah '/upload/'
        // Ini adalah titik penyisipan standar transformasi Cloudinary
        $optimized = str_replace('/upload/', '/upload/q_auto,f_auto/', $url);

        return $optimized;
    }
}
